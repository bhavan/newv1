package com.townwizard.db.resources;

import java.io.IOException;
import java.io.InputStream;

import javax.ws.rs.Consumes;
import javax.ws.rs.FormParam;
import javax.ws.rs.GET;
import javax.ws.rs.POST;
import javax.ws.rs.Path;
import javax.ws.rs.PathParam;
import javax.ws.rs.Produces;
import javax.ws.rs.WebApplicationException;
import javax.ws.rs.core.MediaType;
import javax.ws.rs.core.Response;
import javax.ws.rs.core.Response.Status;

import org.codehaus.jackson.JsonProcessingException;
import org.codehaus.jackson.map.ObjectMapper;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Component;

import com.sun.jersey.api.Responses;
import com.townwizard.db.model.Address;
import com.townwizard.db.model.User;
import com.townwizard.db.services.UserService;
import com.townwizard.db.util.ExceptionHandler;

@Component
@Path("/users")
public class UserResource {
    
    @Autowired
    private UserService userService;
    
    @GET
    @Path("/{userid}")
    @Produces(MediaType.APPLICATION_JSON)
    public User getUser(@PathParam("userid") long userId) {
        User u = userService.getUserById(userId);
        if (u == null) {
            throw new WebApplicationException(Response
                    .status(Responses.NOT_FOUND)
                    .entity(String.format("User %d not found", userId))
                    .type(MediaType.TEXT_PLAIN).build());
        }
        
        if(u.getAddress() != null) {
            u.getAddress().setUser(null);
        }
        
        return u;
    }
    
    @POST
    @Consumes("application/json")
    public Response createUser(InputStream is) {
        ObjectMapper m = new ObjectMapper();
        User user = null;        
        try {            
          user = m.readValue(is, User.class);
        } catch (JsonProcessingException e) {
            ExceptionHandler.handle(e);
            return Response.status(Status.BAD_REQUEST).build();
        } catch (IOException e) {
            ExceptionHandler.handle(e);
            return Response.status(Status.INTERNAL_SERVER_ERROR).build();
        }
        
        return createUser(user);
    }
    
    @POST
    @Consumes("application/x-www-form-urlencoded")
    public Response createUser(
            @FormParam ("email") String email, 
            @FormParam ("password") String password,
            @FormParam ("username") String username,
            @FormParam ("first_name") String firstName,
            @FormParam ("last_name") String lastName,
            @FormParam ("year") Integer year,
            @FormParam ("gender") String gender,
            @FormParam ("mobile_phone") String mobilePhone,
            @FormParam ("address1") String address1,
            @FormParam ("address2") String address2,
            @FormParam ("city") String city,
            @FormParam ("state") String state,
            @FormParam ("postal_code") String postalCode,
            @FormParam ("country") String country) {        
        
        User u = new User();
        u.setEmail(email);
        u.setPassword(password);
        u.setUsername(username);
        u.setFirstName(firstName);
        u.setLastName(lastName);
        u.setYear(year);
        u.setGender(gender);
        u.setMobilePhone(mobilePhone);        
        
        Address a = new Address();
        a.setAddress1(address1);
        a.setAddress2(address2);
        a.setCity(city);
        a.setState(state);
        a.setPostalCode(postalCode);
        a.setCountry(country);        
        u.setAddress(a);
        
        return createUser(u);
    }
    
    private Response createUser(User user) {
        try {            
            if(!user.isValid()) {              
                return Response.status(Status.BAD_REQUEST).build();
            }
            userService.create(user);
          } catch (Exception e) {
              ExceptionHandler.handle(e);
              return Response.status(Status.INTERNAL_SERVER_ERROR).build();
          }
          return Response.status(Status.CREATED).build();        
    }
}