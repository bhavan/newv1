package com.townwizard.db.dao;

import com.townwizard.db.model.User;

public interface UserDao extends AbstractDao {
    
    User getByEmail(String email);
    
}