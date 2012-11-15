package com.townwizard.db.model;

import static org.junit.Assert.assertEquals;
import static org.junit.Assert.assertNotNull;
import static org.junit.Assert.assertNull;

import java.util.Date;

import org.hibernate.Query;
import org.hibernate.Session;
import org.junit.Test;

import com.townwizard.db.test.TestSupport;

public class EntityTest extends TestSupport {
    
    @Test
    public void testUserMapping() {
        Session session = null;
        try {
            session = getSessionFactory().openSession();        
            session.beginTransaction();
            
            User u = createUserWithAddress();
            session.save(u);
            Long id = u.getId();
            assertNotNull("User id should not be null after save()", id);
                    
            User fromDb = getById(session, id);
            assertNotNull("User should be found in db after save() by id", fromDb);
            assertNotNull("User in DB should have address", fromDb.getAddress());
            assertNotNull("User's address should have an id", fromDb.getAddress().getId());
            
            fromDb.setFirstName("Vlad");
            fromDb.getAddress().setAddress2("Front");
            session.save(fromDb);
            session.flush();
            
            fromDb = getById(session, id);
            assertEquals("First name should change", "Vlad", fromDb.getFirstName());
            assertEquals("Address2 should change", "Front", fromDb.getAddress().getAddress2());
                    
            session.delete(fromDb);
            session.flush();
                                  
            fromDb = getById(session, id);
            assertNull("The user should not be found after delete()", fromDb);
            
            session.getTransaction().rollback();
        } finally {
            if(session != null) {
                session.close();
            }
        }
    }
    
    private User getById(Session session, Long id) {
        Query query = session.createQuery("from User where id = :id and active = true").setLong("id", id);
        return (User)query.uniqueResult();
    }
    
    private User createUserWithAddress() {
        Date now = new Date();
        User u = new User();
        u.setCreated(now);
        u.setUpdated(now);
        u.setUsername("j2vm");
        u.setEmail("test_user@test.com");
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
