package com.townwizard.db.resources;

import java.io.InputStream;
import java.util.ArrayList;
import java.util.List;

import javax.ws.rs.Consumes;
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

import com.townwizard.db.model.Content.ContentType;
import com.townwizard.db.model.dto.RatingDTO;
import com.townwizard.db.services.ContentService;

@Component
@Path("/ratings")
public class RatingResource extends ResourceSupport {
    
    @Autowired
    private ContentService contentService;
    
    @GET
    @Path("/{contenttype}/{siteid}/{userid}/{contentids}")
    @Produces(MediaType.APPLICATION_JSON)
    public RatingDTO[] getRatings(
            @PathParam("contenttype") String contentTypeStr,
            @PathParam("siteid") Integer siteId,
            @PathParam("userid") Long userId,
            @PathParam("contentids") String contentIds) {
        
        RatingDTO[] ratings = null;
        try {
            List<Long> externalContentIds = new ArrayList<>();
            for(String contentIdStr : contentIds.split(",")) {
                externalContentIds.add(Long.parseLong(contentIdStr));
            }
            
            ContentType contentType = ContentType.valueOf(contentTypeStr); 
            Long[] ratingContentIds = externalContentIds.toArray(new Long[]{});
            Float[] ratingValues = contentService.getUserRatings(
                    userId, siteId, contentType, ratingContentIds);
            
            ratings = new RatingDTO[ratingValues.length];
            for(int i = 0; i < ratingValues.length; i++) {
                ratings[i] = new RatingDTO(
                        userId, siteId, ratingContentIds[i], ratingValues[i], contentType);
            }
        } catch (Exception e) {
            handleGenericException(e);
        }
        
        return ratings;
    }    

    @POST
    @Consumes(MediaType.APPLICATION_JSON)
    @Produces(MediaType.APPLICATION_JSON)
    public Response createRating(InputStream is) {
        RatingDTO rating = null;
        try {
           rating = parseJson(RatingDTO.class, is);
        } catch(Exception e) {
            handleGenericException(e);
        }
        
        if(rating == null || !rating.isValid()) {
            throw new WebApplicationException(Response
                    .status(Status.BAD_REQUEST)
                    .entity("Cannot create rating: missing or invalid data")
                    .type(MediaType.TEXT_PLAIN).build());
        }

        try {
            Long id = contentService.saveUserRating(rating.getUserId(), rating.getSiteId(), 
                    rating.getContentType(), rating.getContentId(), rating.getValue());
            if(id == null) {
                sendServerError(new Exception("Problem saving rating: rating id is null"));
            }
        } catch(Exception e) {
            handleGenericException(e);
        }
        
        return Response.status(Status.CREATED).build();
    }
}
