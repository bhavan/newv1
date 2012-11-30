package com.townwizard.db.dao;

import java.util.Date;

import org.hibernate.Session;
import org.hibernate.SessionFactory;
import org.springframework.beans.factory.annotation.Autowired;

import com.townwizard.db.model.AbstractEntity;
import com.townwizard.db.model.AuditableEntity;

public class AbstractDaoHibernateImpl implements AbstractDao {

    @Autowired
    private SessionFactory sessionFactory;

    protected Session getSession() {
        return sessionFactory.getCurrentSession();
    }
    
    public <T extends AbstractEntity> Long create(T entity) {
        return (Long) getSession().save(entity);
    }
    
    public <T extends AbstractEntity> void update(T entity) {
        entity.setActive(true);
        if(entity instanceof AuditableEntity) {
            ((AuditableEntity) entity).setUpdated(new Date());
        }
        getSession().update(entity);
    }
    
    public <T extends AbstractEntity> void delete(T entity) {
        getSession().delete(entity);
    }
    
    @SuppressWarnings("unchecked")
    public <T extends AbstractEntity> T getById(Class<T> klass, Long id) {
        T entity = (T)getSession().get(klass, id);
        if (entity != null && Boolean.TRUE.equals(entity.getActive())) {
            return entity;
        }
        return null;
    }
    
}