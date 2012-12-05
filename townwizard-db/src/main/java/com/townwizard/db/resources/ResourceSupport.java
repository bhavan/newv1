package com.townwizard.db.resources;

import java.io.IOException;
import java.io.InputStream;
import java.util.HashMap;
import java.util.Map;

import javax.ws.rs.WebApplicationException;
import javax.ws.rs.core.MediaType;
import javax.ws.rs.core.Response;
import javax.ws.rs.core.Response.Status;

import org.codehaus.jackson.JsonProcessingException;
import org.codehaus.jackson.Version;
import org.codehaus.jackson.map.ObjectMapper;
import org.codehaus.jackson.map.module.SimpleModule;
import org.springframework.beans.factory.annotation.Autowired;

import com.townwizard.db.services.UserService;
import com.townwizard.db.util.ExceptionHandler;
import com.townwizard.db.util.jackson.NullStringDeserializer;

public abstract class ResourceSupport {
    
    public static final String EMPTY_JSON = "{}";
    
    @Autowired
    private UserService userService;
    private static ObjectMapper objectMapper = initializeObjectMapper();    
    

    protected void sendServerError(Exception e) {
        throw new WebApplicationException(Response
                .status(Status.INTERNAL_SERVER_ERROR)
                .entity("Server error: " + e.getMessage())
                .type(MediaType.TEXT_PLAIN).build());
    }
    
    protected void handleGenericException(Exception e) {
        if(!(e instanceof WebApplicationException)) {
            ExceptionHandler.handle(e);
            sendServerError(e);
        } else {
            throw (WebApplicationException)e;
        }
    } 
    
    protected <T> T parseJson(Class<T> entityClass, InputStream is) {
        T entity = null;
        try {
            entity = objectMapper.readValue(is, entityClass);
          } catch (JsonProcessingException e) {
              ExceptionHandler.handle(e);
              throw new WebApplicationException(Response
                      .status(Status.BAD_REQUEST).entity("Cannot parse JSON: " + e.getMessage())
                      .type(MediaType.TEXT_PLAIN).build());
          } catch (IOException e) {
              ExceptionHandler.handle(e);
              sendServerError(e);
              return null;
          }
        return entity;
    }
    
    @SuppressWarnings("unchecked")
    protected Map<String, Object> parseJson(String json) throws IOException, JsonProcessingException {      
        return objectMapper.readValue(json, HashMap.class);
    }
    
    private static ObjectMapper initializeObjectMapper() {
        ObjectMapper m = new ObjectMapper();
        SimpleModule testModule = new SimpleModule("MyModule", new Version(1, 0, 0, null))
                     .addDeserializer(String.class, new NullStringDeserializer());
        m.registerModule(testModule);
        return m;
    }
    
}
