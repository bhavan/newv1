package com.townwizard.db.services;

import com.townwizard.db.model.User;

public interface UserService {
    User getById(Long id);
    User getByEmail(String email);
    Long create(User user);    
}
