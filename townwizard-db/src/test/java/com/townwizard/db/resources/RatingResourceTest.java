package com.townwizard.db.resources;

import java.io.StringReader;
import java.util.List;

import org.apache.http.StatusLine;
import org.codehaus.jackson.map.ObjectMapper;
import org.hibernate.Query;
import org.hibernate.Session;
import org.junit.Assert;
import org.junit.Test;

import com.townwizard.db.model.Content;
import com.townwizard.db.model.Rating;
import com.townwizard.db.model.User;
import com.townwizard.db.model.dto.RatingDTO;

public class RatingResourceTest extends ResourceTest {

    private static final Long TEST_CONTENT_ID = 123456789L;    
    
    @Test
    public void testGetForUnexistingRating() {
        try {
            String getUrl = "/ratings/LOCATION/15/9999999/987654321";
            String response = executeGetRequest(getUrl);
            RatingDTO rating = ratingFromJson(response);
            Assert.assertTrue("Rating must not be null", rating != null);            
            if(rating != null) {
                Assert.assertTrue("Rating must be invalid", !rating.isValid());
                Assert.assertEquals("Rating value must be null", null, rating.getValue());
            }
        } catch (Exception e) {
            e.printStackTrace();
            Assert.fail(e.getMessage());
        }
    }
    
    @Test
    public void testPostAndGet() {
        String email = "rating_test_user@test.com";
        try {
            deleteTestRatingAndContent();
            deleteUserByEmail(email);            
            
            createTestUserViaService(email);
            User u = getUserByEmailFromTheService(email);
            
            StatusLine statusLine = executePostJsonRequest("/ratings", getRatingJson(u.getId(), 4.0F));
            int status = statusLine.getStatusCode();
            Assert.assertEquals(
                    "HTTP status should be 201 (created) when creating rating", 201, status);
            
            String getUrl = "/ratings/LOCATION/15/" + u.getId() + "/" + TEST_CONTENT_ID;
            String response = executeGetRequest(getUrl);
            RatingDTO rating = ratingFromJson(response);
            Assert.assertTrue("A valid rating must be retrieved", rating != null && rating.isValid());
            if(rating != null) {
                Assert.assertEquals("Rating value should match", new Float(4.0), rating.getValue());
            }
            
            statusLine = executePostJsonRequest("/ratings", getRatingJson(u.getId(), 4.5F));
            status = statusLine.getStatusCode();
            Assert.assertEquals(
                    "HTTP status should be 201 (created) when updating rating", 201, status);
            
            
            response = executeGetRequest(getUrl);
            rating = ratingFromJson(response);
            Assert.assertTrue("A valid rating must be retrieved", rating != null && rating.isValid());
            if(rating != null) {
                Assert.assertEquals("Rating value should change", new Float(4.5), rating.getValue());
            }
        } catch (Exception e) {
            e.printStackTrace();
            Assert.fail(e.getMessage());
        } finally {
            deleteTestRatingAndContent();
            deleteUserByEmail(email);
        }
    }
    
    private void createTestUserViaService(String email) throws Exception {
        StatusLine statusLine = executePostJsonRequest("/users", getUserJson(email));
        int status = statusLine.getStatusCode();
        if(status != 201) {
            throw new Exception("Problem creating test user");
        }
    }
    
    protected User getUserByEmailFromTheService(String email) throws Exception {
        String response = executeGetRequest("/users/1/" + email);
        return userFromJson(response);
    }    
    
    private String getUserJson(String email) {
        return "{\"email\":\"" + email + "\",\"password\":\"secret\"}";
    }
    
    private String getRatingJson(Long userId, float value) {
        return "{\"userId\":" + userId + 
                ",\"siteId\":15,\"contentId\":" + TEST_CONTENT_ID + 
                ",\"value\":" + value +",\"contentType\":\"LOCATION\"}";
    }
    
    private RatingDTO ratingFromJson(String json) throws Exception {
        ObjectMapper m = new ObjectMapper();
        RatingDTO[] ratings = m.readValue(new StringReader(json), RatingDTO[].class);
        if(ratings.length > 0) return ratings[0];
        return null;
    }
        
    private void deleteTestRatingAndContent() {
        Session session = null;
        try {
            session = getSessionFactory().openSession();
            session.beginTransaction();            

            Query q = session.createQuery("from Rating where content.externalId = :external_id")
                    .setLong("external_id", TEST_CONTENT_ID);
            @SuppressWarnings("unchecked")
            List<Rating> ratings = q.list();
            for(Rating r : ratings) {
                session.delete(r);
            }
            
            q = session.createQuery("from Content where externalId = :external_id")
                    .setLong("external_id", TEST_CONTENT_ID);
            @SuppressWarnings("unchecked")
            List<Content> contents = q.list();
            for(Content c : contents) {
                session.delete(c);
            }
            
            session.getTransaction().commit();
        } finally {
            if(session != null) {
                session.close();
            }
        }
    }
    
}