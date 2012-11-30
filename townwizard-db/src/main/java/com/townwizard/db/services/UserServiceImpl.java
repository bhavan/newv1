package com.townwizard.db.services;

import org.jasypt.util.password.PasswordEncryptor;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Component;
import org.springframework.transaction.annotation.Transactional;

import com.townwizard.db.dao.UserDao;
import com.townwizard.db.model.User;
import com.townwizard.db.model.User.LoginType;

@Component("userService")
@Transactional
public class UserServiceImpl implements UserService {    
    
    @Autowired
    private UserDao userDao;
    @Autowired
    private PasswordEncryptor passwordEncryptor;

    @Override
    public User getById(Long id) {
        return userDao.getById(User.class, id);
    }
    
    @Override
    public User getByEmailAndLoginType(String email, LoginType loginType) {
        return userDao.getByEmailAndLoginType(email, loginType);
    }
    
    @Override
    public User login(String email, String password) {
        User user = userDao.getByEmailAndLoginType(email, LoginType.TOWNWIZARD);
        if(user != null) {
            if(passwordEncryptor.checkPassword(password, user.getPassword())) {
                return user;
            }
        }
        return null;
    }
    
    @Override
    public Long create(User user) {
        encryptPassword(user);
        userDao.create(user);
        return user.getId();
    }
    
    @Override
    public void update(User user) {
        encryptPassword(user);
        userDao.update(user);        
    }
    
    private void encryptPassword(User user) {
        String plainPassword = user.getPassword();
        if(plainPassword != null) {
            user.setPassword(passwordEncryptor.encryptPassword(plainPassword));
        }
    }
}