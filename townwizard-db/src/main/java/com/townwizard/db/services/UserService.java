package com.townwizard.db.services;

import com.townwizard.db.model.User;

public interface UserService {
    User getUserById(Long id);
}
