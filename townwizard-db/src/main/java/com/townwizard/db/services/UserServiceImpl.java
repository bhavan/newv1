package com.townwizard.db.services;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Component;
import org.springframework.transaction.annotation.Transactional;

import com.townwizard.db.dao.UserDao;
import com.townwizard.db.model.User;

@Component("userService")
@Transactional
public class UserServiceImpl implements UserService {    
    
    @Autowired
    private UserDao userDao;    

    @Override
    public User getById(Long id) {
        return userDao.getById(User.class, id);
    }
    
    @Override
    public User getByEmail(String email) {
        return userDao.getByEmail(email);
    }    
    
    @Override
    public void create(User user) {
        userDao.create(user);
    }
}