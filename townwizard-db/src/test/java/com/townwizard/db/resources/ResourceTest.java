package com.townwizard.db.resources;

import java.io.StringReader;
import java.io.UnsupportedEncodingException;
import java.util.ArrayList;
import java.util.List;
import java.util.Map;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.NameValuePair;
import org.apache.http.StatusLine;
import org.apache.http.client.HttpClient;
import org.apache.http.client.entity.UrlEncodedFormEntity;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.entity.StringEntity;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.message.BasicNameValuePair;
import org.codehaus.jackson.map.ObjectMapper;
import org.glassfish.grizzly.http.server.HttpServer;
import org.hibernate.Query;
import org.hibernate.Session;
import org.junit.AfterClass;
import org.junit.BeforeClass;

import com.townwizard.db.application.Standalone;
import com.townwizard.db.model.User;
import com.townwizard.db.test.TestSupport;
import com.townwizard.db.util.HttpUtils;

public abstract class ResourceTest extends TestSupport {
    
    private static HttpServer httpServer;
    
    @BeforeClass
    public static void beforeTestsRun() throws Exception {
        TestSupport.beforeTestsRun();
        if(!isServiceRunning()) {
            httpServer = Standalone.startServer();
        }
    }
    
    @AfterClass
    public static void afterTestsRun() throws Exception {
        TestSupport.afterTestsRun();
        if(httpServer != null) {
            httpServer.stop();
        }
    }
    
    protected String executeGetRequest(String path) {
        return HttpUtils.executeGetRequest(getWebServicesUrlBase() + path);
    }

    protected StatusLine executePostRequest(String path, HttpEntity entity, String contentType) {
        StatusLine statusLine = null;
        try {
            HttpClient c = new DefaultHttpClient();
            HttpPost post = new HttpPost(getWebServicesUrlBase() + path);
            post.setEntity(entity);
            post.setHeader("Content-Type", contentType);
            HttpResponse response = c.execute(post);
            statusLine = response.getStatusLine();
            c.getConnectionManager().shutdown();
        } catch(Throwable e) {
            if(statusLine != null) {
                System.out.println(statusLine);
            }
            e.printStackTrace();
        }
        return statusLine;
    }
    
    protected StatusLine executePostJsonRequest(String path, String entity) {
        try {
          return executePostRequest(path, new StringEntity(entity), "application/json");
        } catch (UnsupportedEncodingException e) {
          e.printStackTrace();
          return null;
        }
    }
    
    protected StatusLine executePostFormRequest(String path, Map<String, String> parameters) {
        try {
            List<NameValuePair> params = new ArrayList<>();
            for(Map.Entry<String, String> e : parameters.entrySet()) {
                params.add(new BasicNameValuePair(e.getKey(), e.getValue()));
            }
            return executePostRequest(path, new UrlEncodedFormEntity(params), "application/x-www-form-urlencoded");
        } catch (UnsupportedEncodingException e) {
            e.printStackTrace();
            return null;
        }
    }
    
    protected User getUserByEmailFromTheService(String email) throws Exception {
        String response = executeGetRequest("/users/1/" + email);
        return userFromJson(response);
    }
    
    protected User userFromJson(String json) throws Exception {
        ObjectMapper m = new ObjectMapper();
        User u = m.readValue(new StringReader(json), User.class);
        return u;
    }
    
    protected void deleteUserByEmail(String email) {
        Session session = null;
        try {
            session = getSessionFactory().openSession();
            session.beginTransaction();
            Query q = session.createQuery("from User where email = :email").setString("email", email);
            @SuppressWarnings("unchecked")
            List<User> users = q.list();
            for(User u : users) {
              session.delete(u);
            }
            session.getTransaction().commit();
        } finally {
            if(session != null) {
                session.close();
            }
        }
    }    
    
    private static boolean isServiceRunning() {
        try {
            HttpClient c = new DefaultHttpClient();
            HttpGet get = new HttpGet(getWebServicesUrlBase());
            HttpResponse response = c.execute(get);
            int statusCode = response.getStatusLine().getStatusCode();
            return (statusCode == 404);
        } catch (Throwable e) {
            return false;
        }        
    }
    
}