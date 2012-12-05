package com.townwizard.db.model;

import static org.junit.Assert.assertEquals;
import static org.junit.Assert.assertNotNull;
import static org.junit.Assert.assertNull;
import static org.junit.Assert.fail;

import org.hibernate.Query;
import org.hibernate.Session;
import org.junit.After;
import org.junit.Before;
import org.junit.Test;

import com.townwizard.db.model.Content.ContentType;
import com.townwizard.db.model.User.LoginType;
import com.townwizard.db.test.TestSupport;

public class EntityTest extends TestSupport {
    
    private Session session;
    
    @Before
    public void beginTransaction() {
        session = getSessionFactory().openSession();        
        session.beginTransaction();        
    }
    
    @After
    public void rollbackTransaction() {
        try {
            if(session != null) {
                session.getTransaction().rollback();                
            }
        } finally {
            if(session != null) {
                session.close();
            }
        }
    }
    
    @Test
    public void testContentMapping() {
        try {
            Content c = createContent();
            session.save(c);
            Long id = c.getId();
            assertNotNull("Content id should not be null after save()", id);            
            
            Content fromDb = getContentById(id);
            assertNotNull("Content should be found in db after save() by id", fromDb);
            assertEquals("Content type should be correct", ContentType.LOCATION, fromDb.getContentType());
            fromDb.setExternalId(2L);
            fromDb.setContentType(ContentType.EVENT);
            session.save(fromDb);
            
            fromDb = getContentById(id);
            assertEquals("Content external id should change", new Long(2), fromDb.getExternalId());
            assertEquals("Content type should change", ContentType.EVENT, fromDb.getContentType());
            
            session.delete(fromDb);
            fromDb = getContentById(id);
            assertNull("Content should not be found after delete()", fromDb);
        } catch(Exception e) {
            e.printStackTrace();
            fail(e.getMessage());
        }
    }
    
    @Test
    public void testUserMapping() {
        try {
            User u = createUserWithAddress();
            session.save(u);
            Long id = u.getId();
            assertNotNull("User id should not be null after save()", id);
    
            User fromDb = getUserById(id);
            assertNotNull("User should be found in db after save() by id", fromDb);
            assertNotNull("User in DB should have address", fromDb.getAddress());
            assertNotNull("User's address should have an id", fromDb.getAddress().getId());
    
            fromDb.setFirstName("Vlad");
            fromDb.getAddress().setAddress2("Front");
            session.save(fromDb);
    
            fromDb = getUserById(id);           
            assertEquals("Address2 should change", "Front", fromDb.getAddress().getAddress2());          

            session.delete(fromDb);
    
            fromDb = getUserById(id);
            assertNull("User should not be found after delete()", fromDb);
        } catch (Exception e) {
            e.printStackTrace();
            fail(e.getMessage());
        }
    }
    
    @Test
    public void testRatingMapping() {
        try {
            User u = createUserWithAddress();
            session.save(u);
            Content c = createContent();
            session.save(c);
            
            Rating r = new Rating();
            r.setUser(u);
            r.setContent(c);
            r.setValue(4.5f);
            
            session.save(r);
            Long id = r.getId();
            assertNotNull("Rating id should not be null after save()", id);
            
            Rating fromDb = getRatingById(id);
            assertNotNull("Rating should be found in db after save() by id", fromDb);
            assertNotNull("Rating should have a user", fromDb.getUser());
            assertNotNull("Rating should have a content", fromDb.getContent());
            assertNotNull("Rating should have a value", fromDb.getValue());
            
            fromDb.setValue(4.1F);
            session.save(fromDb);
            
            fromDb = getRatingById(id);
            assertEquals("Rating value should change", new Float(4.1), fromDb.getValue());            
            
            session.delete(r);
            fromDb = getRatingById(id);
            assertNull("Rating should not be found after delete()", fromDb);
            
            session.delete(c);
            session.delete(u);
        } catch (Exception e) {
            e.printStackTrace();
            fail(e.getMessage());
        }
    }
    
    private User getUserById(Long id) {
        Query query = session.createQuery("from User where id = :id and active = true").setLong("id", id);
        return (User)query.uniqueResult();
    }
    
    private Content getContentById(Long id) {
        Query query = session.createQuery("from Content where id = :id and active = true").setLong("id", id);
        return (Content)query.uniqueResult();
    }
    
    private Rating getRatingById(Long id) {
        Query query = session.createQuery("from Rating where id = :id and active = true").setLong("id", id);
        return (Rating)query.uniqueResult();
    }    
    
    private User createUserWithAddress() {
        User u = new User();
        u.setUsername("j2vm");
        u.setEmail("test_user@test.com");
        u.setPassword("secret");
        u.setFirstName("Vladimir");
        u.setLastName("Mazheru");
        u.setYear(1968);
        u.setGender("M");
        u.setMobilePhone("917-439-7193");
        u.setRegistrationIp("192.168.112.231");
        u.setLoginType(LoginType.FACEBOOK);
        u.setExternalId(123456L);        
                
        Address a = new Address();
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
    
    private Content createContent() {
        Content c = new Content();
        c.setSiteId(getSiteIdByName("demo.townwizard.com"));
        c.setExternalId(1L);
        c.setContentType(ContentType.LOCATION);
        c.setActive(true);
        return c;
    }
    
    private Integer getSiteIdByName(String siteName) {
        Query q = session.createSQLQuery("SELECT mid FROM master WHERE site_url = ?")
                .setString(0, siteName);
        return (Integer)q.uniqueResult();
    }
}
