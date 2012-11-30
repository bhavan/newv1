package com.townwizard.db.services;

import com.townwizard.db.model.User;
import com.townwizard.db.model.User.LoginType;

public interface UserService {
    User getById(Long id);
    User getByEmailAndLoginType(String email, LoginType loginType);
    User login(String email, String password);
    Long create(User user);
    void update(User user);
}
