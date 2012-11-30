package com.townwizard.db.dao;

import org.hibernate.Query;
import org.springframework.stereotype.Component;

import com.townwizard.db.model.User;
import com.townwizard.db.model.User.LoginType;

@Component("userDao")
public class UserDaoHibernateImpl extends AbstractDaoHibernateImpl implements UserDao {

    @Override
    public User getByEmailAndLoginType(String email, LoginType loginType) {
        Query q = getSession().createQuery(
                "from User where email = :email and loginType = :login_type and active = true")
                .setString("email", email)
                .setInteger("login_type", loginType.getId());
        return (User)q.uniqueResult();
    }
    
}