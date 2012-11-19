package com.townwizard.db.dao;

import org.hibernate.Query;
import org.springframework.stereotype.Component;

import com.townwizard.db.model.User;

@Component("userDao")
public class UserDaoHibernateImpl extends AbstractDaoHibernateImpl implements UserDao {

    @Override
    public User getByEmail(String email) {
        Query q = getSession().createQuery("from User where email = :email and active = true")
                .setString("email", email);
        return (User)q.uniqueResult();
    }

    @Override
    public User getByEmailAndPassword(String email, String password) {
        Query q = getSession().createQuery(
                "from User where email = :email and password = :password and active = true")
                .setString("email", email)
                .setString("password", password);
        return (User)q.uniqueResult();
    }
    
}