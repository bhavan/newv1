package com.townwizard.db.resources;

import java.net.URL;
import java.util.Collections;
import java.util.Date;
import java.util.HashMap;
import java.util.Iterator;
import java.util.Map;
import java.util.UUID;

import javax.ws.rs.GET;
import javax.ws.rs.Path;
import javax.ws.rs.Produces;
import javax.ws.rs.QueryParam;
import javax.ws.rs.core.MediaType;
import javax.ws.rs.core.Response;
import javax.ws.rs.core.Response.Status;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Component;

import com.townwizard.db.model.LoginRequest;
import com.townwizard.db.model.User;
import com.townwizard.db.services.UserService;
import com.townwizard.db.util.HttpUtils;

@Component
@Path("/login")
public class FacebookLoginResource extends UserResource {
    
    private static final String FB_APP_ID = "373685232723588";
    private static final String FB_APP_SECRET = "d9c84a8e96b422fe8075360a8415f584";
    private static final String FB_LOGIN_RESOURCE = "http://tw-db.com/login/fb";
    private static final String PHP_LOGIN_PATH = "/townwizard-db-api/fb-login.php";
  
    @Autowired
    private UserService userService;
    
    @GET
    @Path("/fb")
    @Produces(MediaType.TEXT_HTML)
    public Response loginWithFacebook(
            @QueryParam("code") String code,
            @QueryParam("state") String loginRequestId,
            @QueryParam("l") String location,
            @QueryParam("uid") Integer userId) {

        if(userId != null && location != null) {
            String server = getServerPartFromLocationUrl(location);
            StringBuilder html = new StringBuilder();
            html.append("<!DOCTYPE HTML>");
            html.append("<html><head><meta charset=\"UTF-8\"><meta http-equiv=\"Content-type\" content=\"text/html;charset=UTF-8\"></head><body>");
            html.append("<script>");
            html.append("window.location.href='").append(server).append(PHP_LOGIN_PATH)
                .append("?uid=").append(userId).append("&l=").append(location).append("';");            
            html.append("</script>");
            html.append("</body></html>");
            return Response.status(Status.OK).entity(html.toString()).build();
        }
        
        if(code == null && loginRequestId == null) {
            StringBuilder sb = new StringBuilder();
            sb.append("https://www.facebook.com/dialog/oauth?");
            sb.append("client_id=").append(FB_APP_ID);
            sb.append("&redirect_uri=").append(FB_LOGIN_RESOURCE);
            String lRequestId = UUID.randomUUID().toString();
            sb.append("&state=").append(lRequestId);
            sb.append("&display=popup");
            sb.append("&scope=email");            
            String dialogUrl = sb.toString();
            StringBuilder html = new StringBuilder();
            html.append("<!DOCTYPE HTML>");
            html.append("<html><head><meta charset=\"UTF-8\"><meta http-equiv=\"Content-type\" content=\"text/html;charset=UTF-8\"></head><body>");
            html.append("<script>window.location.href='" + dialogUrl + "'</script>");
            html.append("</body></html>");
            
            cacheLoginRequest(new LoginRequest(lRequestId, location, new Date()));
            
            return Response.status(Status.OK).entity(html.toString()).build();
        }
        
        if(code == null && loginRequestId != null) {
            StringBuilder html = new StringBuilder();
            html.append("<!DOCTYPE HTML>");
            html.append("<html><head><meta charset=\"UTF-8\"><meta http-equiv=\"Content-type\" content=\"text/html;charset=UTF-8\"></head><body>");
            html.append("<script>window.close();</script>");
            html.append("</body></html>");            
            return Response.status(Status.OK).entity(html.toString()).build();
        }
        
        LoginRequest lRequest;
        if(code != null && (lRequest = getLoginRequestFromCache(loginRequestId)) != null) {
            StringBuilder sb = new StringBuilder();
            sb.append("https://graph.facebook.com/oauth/access_token?");
            sb.append("client_id=").append(FB_APP_ID);
            sb.append("&redirect_uri=").append(FB_LOGIN_RESOURCE);
            sb.append("&client_secret=").append(FB_APP_SECRET);
            sb.append("&code=").append(code);
                
            String tokenUrl = sb.toString();
            String response = HttpUtils.executeGetRequest(tokenUrl);
            Map<String, String> accessInfo = parseAcessTokenResponse(response);
            String accessToken = accessInfo.get("access_token");            
            
            String userUrl = "https://graph.facebook.com/me?access_token=" + accessToken;
            String userData = HttpUtils.executeGetRequest(userUrl);
            try {
                Map<String, Object> fbUser = parseJson(userData);
                User u = User.fromFbUser(fbUser);
                createOrUpdateExternalUser(u);
                StringBuilder html = new StringBuilder();            
                html.append("<!DOCTYPE HTML>");
                html.append("<html><head><meta charset=\"UTF-8\"><meta http-equiv=\"Content-type\" content=\"text/html;charset=UTF-8\"></head><body>");
                html.append("<script>");
                html.append("window.location.href='").append(FB_LOGIN_RESOURCE)
                    .append("?uid=").append(u.getId()).append("&l=").append(lRequest.getLocation()).append("';");
                html.append("</script>");
                html.append("</body></html>");
                
                return Response.status(Status.OK).entity(html.toString()).build();            
            } catch (Exception e) {
                handleGenericException(e);
            }
        }
        
        return Response.status(Status.BAD_REQUEST).build();
    }
    
    private Map<String, String> parseAcessTokenResponse(String s) {
        //translate the line below into map
        //access_token=AAAFT3VZBN5oQBAOqIik79TCYlzoJLBjtbZA5f3emRu5g0V37E6q5FR0t30sZBsAi3lQgY6ZBnIoxJZCMocr80zjgrkmBWyJtDvUrugshGjwZDZD&expires=5183326
        Map<String, String> result = new HashMap<>();
        String[] data = s.split("&");
        if(data.length == 2) {
            for(String str : data) {
                String[] entry = str.split("=");
                if(entry.length == 2) {
                    result.put(entry[0], entry[1]);
                }
            }
        }
        
        if(result.size() == 2) return result;
        return Collections.emptyMap();
    }
    
    private String getServerPartFromLocationUrl(String location) {
        try {
            URL url = new URL(location);
            String retVal = url.getProtocol() + "://" + url.getHost();
            return retVal;
        } catch (Exception e) {
            handleGenericException(e);
            return null;
        }
    }
    

    
    //TODO: implement login request caching with DB table instead of hashmap
    //in order to remove state handling from the application
    private static final Map<String, LoginRequest> LOGIN_REQUEST_CACHE= new HashMap<>();
    private static final long LOGIN_REQUEST_CACHE_TTL = 30 * 60 * 1000; //30 minutes
    private static final long LOGIN_REQUEST_CACHE_MAX_SIZE = 1000; //30 minutes
    
    private static void cacheLoginRequest(LoginRequest loginRequest) {
        LOGIN_REQUEST_CACHE.put(loginRequest.getLoginId(), loginRequest);
    }    
    
    private static LoginRequest getLoginRequestFromCache(String loginId) {        
        LoginRequest retVal = LOGIN_REQUEST_CACHE.remove(loginId);
        cleanLoginRequestCache();        
        return retVal;
    }
    
    private static void cleanLoginRequestCache() {
        if(LOGIN_REQUEST_CACHE.size() > LOGIN_REQUEST_CACHE_MAX_SIZE) {
            synchronized(LOGIN_REQUEST_CACHE) {
                long now = System.currentTimeMillis();
                Iterator<Map.Entry<String, LoginRequest>> i = LOGIN_REQUEST_CACHE.entrySet().iterator();
                while(i.hasNext()) {
                    Map.Entry<String, LoginRequest> e = i.next();
                    if(now - LOGIN_REQUEST_CACHE_TTL > e.getValue().getCreated().getTime()) {
                        i.remove();
                    }
                }
            }
        }
    }
}
