package com.townwizard.db.dao;

import com.townwizard.db.model.AbstractEntity;

public interface AbstractDao {
    <T extends AbstractEntity> Long create(T entity);    
    <T extends AbstractEntity> void delete(T entity);
    <T extends AbstractEntity> void update(T entity);  
    <T extends AbstractEntity> T getById(Class<T> clazz, Long id);
}