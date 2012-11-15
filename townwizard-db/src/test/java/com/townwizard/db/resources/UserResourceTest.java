package com.townwizard.db.resources;

import org.apache.http.StatusLine;
import org.hibernate.Query;
import org.hibernate.Session;
import org.junit.Assert;
import org.junit.Test;

import com.townwizard.db.model.User;

public class UserResourceTest extends ResourceTest {
    
    @Test
    public void testPostMulformedJson() {
        StatusLine statusLine = executePostJSONRequest("/users", "Not a JSON string");
        int status = statusLine.getStatusCode();
        Assert.assertEquals("HTTP status should be 400 when JSON data is mulformed", 400, status);
    }
    
    @Test
    public void testPostEmptyJson() {
        StatusLine statusLine = executePostJSONRequest("/users", "{}");
        int status = statusLine.getStatusCode();
        Assert.assertEquals("HTTP status should be 400 when JSON is empty", 400, status);
    }

    @Test
    public void testPostUserWithInvalidEmail() {
        String email = "invalid";
        try {
            deleteUserByEmail(email);
            StatusLine statusLine = executePostJSONRequest("/users", getMinimalUserJson(email));
            int status = statusLine.getStatusCode();
            Assert.assertEquals(
                    "HTTP status should be 400 when email is invalid", 400, status);
        } finally {
            deleteUserByEmail(email);
        }
    }
    
    @Test
    public void testPostMinimalUserJson() {
        String email = "min_user@test.com";
        try {
            deleteUserByEmail(email);
            StatusLine statusLine = executePostJSONRequest("/users", getMinimalUserJson(email));
            int status = statusLine.getStatusCode();
            Assert.assertEquals(
                    "HTTP status should be 201 (created) for the minimal user JSON", 201, status);
        } finally {
            deleteUserByEmail(email);
        }
    }
    
    @Test
    public void testPostFullUserJson() {
        String email = "full_user@test.com";
        try {
            deleteUserByEmail(email);
            StatusLine statusLine = executePostJSONRequest("/users", getFullUserJson(email));
            int status = statusLine.getStatusCode();
            Assert.assertEquals(
                    "HTTP status should be 201 (created) for the minimal user JSON", 201, status);
        } finally {
            deleteUserByEmail(email);
        }
    }
    
    private String getMinimalUserJson(String email) {
        return "{\"email\":\"" + email + "\",\"password\":\"secret\"}";
    }
    
    private String getFullUserJson(String email) {
        return "{\"username\":\"j2vm\",\"email\":\"" + email + "\",\"password\":\"secret\",\"firstName\":\"Vlad\",\"lastName\":\"Mazheru\",\"year\":1968,\"gender\":\"M\",\"mobilePhone\":\"917-439-7193\",\"registrationIp\":\"127.0.0.1\",\"address\":{\"address1\":\"324 Nelson Ave\",\"address2\":\"Frnt\",\"city\":\"Staten Island\",\"state\":\"NY\",\"postalCode\":\"10308\",\"country\":\"USA\"}}";
    }
    
    private void deleteUserByEmail(String email) {
        Session session = null;
        try {
            session = getSessionFactory().openSession();
            session.beginTransaction();
            Query q = session.createQuery("from User where email = :email").setString("email", email);
            User u = (User)q.uniqueResult();
            if(u != null) {
              session.delete(u);
            }
            session.getTransaction().commit();
        } finally {
            if(session != null) {
                session.close();
            }
        }
    }
}