package com.townwizard.db.services;

import com.townwizard.db.model.User;

public interface UserService {
    User getById(Long id);
    User getByEmail(String email);
    User login(String email, String password);
    Long create(User user);
    void update(User user);
}
