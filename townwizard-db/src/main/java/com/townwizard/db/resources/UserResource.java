package com.townwizard.db.resources;

import javax.ws.rs.GET;
import javax.ws.rs.Path;
import javax.ws.rs.PathParam;
import javax.ws.rs.Produces;
import javax.ws.rs.WebApplicationException;
import javax.ws.rs.core.MediaType;
import javax.ws.rs.core.Response;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Component;

import com.sun.jersey.api.Responses;
import com.townwizard.db.model.User;
import com.townwizard.db.services.UserService;

@Component
@Path("/users/{userid}")
public class UserResource {
    
    @Autowired
    private UserService userService;
    
    @GET
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
}