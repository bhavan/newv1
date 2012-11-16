package com.townwizard.db.resources;

import java.io.StringReader;
import java.util.Date;

import org.apache.http.StatusLine;
import org.codehaus.jackson.map.ObjectMapper;
import org.hibernate.Query;
import org.hibernate.Session;
import org.junit.Assert;
import org.junit.Test;

import com.townwizard.db.model.Address;
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
    public void testPostJsonWithEmptyStrings() {
        StatusLine statusLine = executePostJSONRequest("/users", "{\"email\":\"\",\"password\":\"     \",\"username\":null}");
        int status = statusLine.getStatusCode();
        Assert.assertEquals("HTTP status should be 400 when JSON email or password is empty", 400, status);
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
        String email = "min_user_json@test.com";
        try {
            deleteUserByEmail(email);
            StatusLine statusLine = executePostJSONRequest("/users", getMinimalUserJson(email));
            int status = statusLine.getStatusCode();
            Assert.assertEquals(
                    "HTTP status should be 201 (created) for the minimal user JSON", 201, status);
            assertUserCreatedCorrectly(getMinimalUserJson(email), email);
        } catch (Exception e) {
            e.printStackTrace();
            Assert.fail(e.getMessage());
        } finally {
            deleteUserByEmail(email);
        }
    }
    
    @Test
    public void testPostFullUserJson() {
        String email = "full_user_json@test.com";
        try {
            deleteUserByEmail(email);
            StatusLine statusLine = executePostJSONRequest("/users", getFullUserJson(email));
            int status = statusLine.getStatusCode();
            Assert.assertEquals(
                    "HTTP status should be 201 (created) for the minimal user JSON", 201, status);
            assertUserCreatedCorrectly(getFullUserJson(email), email);
        } catch (Exception e) {
            e.printStackTrace();
            Assert.fail(e.getMessage());
        } finally {
            deleteUserByEmail(email);
        }
    }
    
    @Test
    public void testPostMinimalUserForm() {
        String email = "min_user_form@test.com";
        try {
            deleteUserByEmail(email);
            User u = new User();
            u.setEmail(email);
            u.setPassword("secret");
            StatusLine statusLine = executePostFormRequest("/users", u.asParametersMap());
            int status = statusLine.getStatusCode();
            Assert.assertEquals(
                    "HTTP status should be 201 (created) for the minimal user form request", 201, status);
            assertUserCreatedCorrectly(u, email);
        } catch (Exception e) {
            e.printStackTrace();
            Assert.fail(e.getMessage());
        } finally {
            deleteUserByEmail(email);
        }
    }
    
    @Test
    public void testPostFullUserForm() {
        String email = "full_user_form@test.com";
        try {
            deleteUserByEmail(email);
            User u = createUserWithAddress(email);
            StatusLine statusLine = executePostFormRequest("/users", u.asParametersMap());
            int status = statusLine.getStatusCode();
            Assert.assertEquals(
                    "HTTP status should be 201 (created) for the minimal user form request", 201, status);
            assertUserCreatedCorrectly(u, email);
        } catch (Exception e) {
            e.printStackTrace();
            Assert.fail(e.getMessage());
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
    
    private void assertUserCreatedCorrectly(String userJson, String email) throws Exception {
        User createdUser = getUserByEmailFromTheService(email);
        User userFromJson = userFromJson(userJson);
        Assert.assertTrue("User created should have the same properties as user submitted", 
                usersEqual(createdUser, userFromJson));
    }
    
    private void assertUserCreatedCorrectly(User user, String email) throws Exception {
        User createdUser = getUserByEmailFromTheService(email);        
        Assert.assertTrue("User created should have the same properties as user submitted", 
                usersEqual(createdUser, user));
    }    
    
    private User getUserByEmailFromTheService(String email) throws Exception {
        String response = executeGetRequest("/users/" + email);
        return userFromJson(response);
    }
    
    private User userFromJson(String json) throws Exception {
        ObjectMapper m = new ObjectMapper();
        User u = m.readValue(new StringReader(json), User.class);
        return u;
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
    
    private boolean usersEqual(User u1, User u2) {
        boolean result = true;
        result &= u1.getEmail().equals(u2.getEmail());
        result &= u1.getPassword().equals(u2.getPassword());
        result &= compareWithNulls(u1.getUsername(), u2.getUsername());
        result &= compareWithNulls(u1.getFirstName(), u2.getFirstName());
        result &= compareWithNulls(u1.getLastName(), u2.getLastName());
        result &= compareWithNulls(u1.getYear(), u2.getYear());
        result &= compareWithNulls(u1.getGender(), u2.getGender());
        result &= compareWithNulls(u1.getMobilePhone(), u2.getMobilePhone());
        result &= addressesEqual(u1.getAddress(), u2.getAddress());
        return result;
    }
    
    @SuppressWarnings("null")
    private boolean addressesEqual(Address a1, Address a2) {
        if(a1 == null && a2 == null) return true;
        if(a1 == null && a2 != null) return false;
        if(a1 != null && a2 == null) return false;
        
        boolean result = true;
        result &= compareWithNulls(a1.getAddress1(), a2.getAddress1());
        result &= compareWithNulls(a1.getAddress2(), a2.getAddress2());
        result &= compareWithNulls(a1.getCity(), a2.getCity());
        result &= compareWithNulls(a1.getState(), a2.getState());
        result &= compareWithNulls(a1.getPostalCode(), a2.getPostalCode());
        result &= compareWithNulls(a1.getCountry(), a2.getCountry());
        return result;
    }
    
    @SuppressWarnings("null")
    private boolean compareWithNulls(Object o1, Object o2) {
        if(o1 == null && o2 == null) return true;
        if(o1 != null && o2 == null) return false;
        if(o1 == null && o2 != null) return false;
        return o1.equals(o2);
    }
    
    private User createUserWithAddress(String email) {
        Date now = new Date();
        User u = new User();
        u.setCreated(now);
        u.setUpdated(now);
        u.setUsername("j2vm");
        u.setEmail(email);
        u.setPassword("secret");
        u.setFirstName("Vladimir");
        u.setLastName("Mazheru");
        u.setYear(1968);
        u.setGender("M");
        u.setMobilePhone("917-439-7193");
        u.setRegistrationIp("192.168.112.231");
        u.setActive(true);
        
        Address a = new Address();
        a.setActive(true);
        a.setCreated(now);
        a.setUpdated(now);
        a.setAddress1("324 Nelson Ave");
        a.setAddress2("Frnt");
        a.setCity("Staten Island");
        a.setPostalCode("10308");
        a.setState("NY");
        a.setCountry("USA");
        u.setAddress(a);
        a.setUser(u);
        
        return u;
    }   
}