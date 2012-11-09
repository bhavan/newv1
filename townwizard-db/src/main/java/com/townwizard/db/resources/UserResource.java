package com.townwizard.db.resources;

import javax.ws.rs.GET;
import javax.ws.rs.Path;
import javax.ws.rs.PathParam;
import javax.ws.rs.Produces;
import javax.ws.rs.WebApplicationException;
import javax.ws.rs.core.MediaType;
import javax.ws.rs.core.Response;

import com.sun.jersey.api.Responses;

import com.townwizard.db.model.User;

@Path("/users/{userid}")
public class UserResource {
    @GET
    @Produces(MediaType.APPLICATION_JSON)
    public User getUser(@PathParam("userid") int userId) {
        if (userId < 1) {
            throw new WebApplicationException(Response
                    .status(Responses.NOT_FOUND)
                    .entity(String.format("User %d not found", userId))
                    .type(MediaType.TEXT_PLAIN).build());
        }
        User u = new User();
        u.setFirstName("Vladimir3adasdasdads");
        u.setLastName("Mazheru");
        return u;
    }
}