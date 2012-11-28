package com.townwizard.db.resources;

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

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Component;

import com.townwizard.db.model.Address;
import com.townwizard.db.model.User;
import com.townwizard.db.services.UserService;
import com.townwizard.db.util.ExceptionHandler;

@Component
@Path("/users")
public class UserResource extends ResourceSupport {
    
    @Autowired
    private UserService userService;
    
    @GET
    @Path("/{userid}")
    @Produces(MediaType.APPLICATION_JSON)
    public User getUser(@PathParam("userid") String userId) {
        User u = null;
        try {
            Long id = null;
            try { id = Long.parseLong(userId); } catch (NumberFormatException e) {/*nothing*/}            
            
            if(id == null) {
                u = userService.getByEmail(userId);
            } else {                
                u = userService.getById(id);
            }
        } catch (Exception e) {
            ExceptionHandler.handle(e);
            sendServerError(e);
        }
        
        if (u == null) {
            throw new WebApplicationException(Response
                    .status(Status.NOT_FOUND)
                    .entity(EMPTY_JSON)
                    .type(MediaType.APPLICATION_JSON).build());
        }
        return u.asJsonView();
    }
    
    @POST
    @Path("/login")
    @Consumes(MediaType.APPLICATION_JSON)
    @Produces(MediaType.APPLICATION_JSON)
    public User login(InputStream is) {
        User u = null;
        User fromJson = parseJson(User.class, is);
        try {
            u = userService.login(fromJson.getEmail(), fromJson.getPassword());
        } catch (Exception e) {
            ExceptionHandler.handle(e);
            sendServerError(e);
        }
        
        if (u == null) {
            throw new WebApplicationException(Response
                    .status(Status.NOT_FOUND)
                    .entity(EMPTY_JSON)
                    .type(MediaType.APPLICATION_JSON).build());
        }
        return u.asJsonView();
    }
    
    @POST
    @Path("/loginwith")
    @Consumes(MediaType.APPLICATION_JSON)
    @Produces(MediaType.APPLICATION_JSON)
    public User loginWith(InputStream is) {
        User u = null;
        User fromJson = parseJson(User.class, is);
        try {
            u = userService.getByEmail(fromJson.getEmail());
            if(u == null) { //first time login
                u = fromJson;
                u.setPassword("facebook" + u.getEmail() + Math.random());
                createUser(u);
            }
        } catch (Exception e) {
            ExceptionHandler.handle(e);
            sendServerError(e);
        }
        
        if (u == null) {
            throw new WebApplicationException(Response
                    .status(Status.NOT_FOUND)
                    .entity(EMPTY_JSON)
                    .type(MediaType.APPLICATION_JSON).build());
        }
        return u.asJsonView();
    }    
    
    @POST
    @Consumes(MediaType.APPLICATION_JSON)
    @Produces(MediaType.APPLICATION_JSON)
    public Response createUser(InputStream is) {
        User user = parseJson(User.class, is);
        return createUser(user);
    }
    
    @POST
    @Consumes(MediaType.APPLICATION_FORM_URLENCODED)
    @Produces(MediaType.APPLICATION_JSON)
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
        if(user == null || !user.isValid()) {
            throw new WebApplicationException(Response
                    .status(Status.BAD_REQUEST)
                    .entity("Cannot create user: missing or invalid user data")
                    .type(MediaType.TEXT_PLAIN).build());
        }
        if(userService.getByEmail(user.getEmail()) != null) {
            throw new WebApplicationException(Response
                    .status(Status.CONFLICT)
                    .entity(String.format("User with email %s already exists", user.getEmail()))
                    .type(MediaType.TEXT_PLAIN).build());            
        }
        if(user.getAddress() != null && !user.getAddress().isValid()) {
            user.setAddress(null);
        }
        try {
            Long id = userService.create(user);
            if(id == null) {
                sendServerError(new Exception("Problem creating user: user id is null"));
            }
        } catch(Exception e) {
            sendServerError(e);
        }
        return Response.status(Status.CREATED).entity(user).build();
    }
}