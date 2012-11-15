package com.townwizard.db.model;

import java.io.Serializable;
import java.util.Date;

import org.hibernate.EmptyInterceptor;
import org.hibernate.type.Type;
import org.springframework.stereotype.Component;

@Component("entityInterceptor")
public class EntityInterceptor extends EmptyInterceptor {
    
    private static final long serialVersionUID = 1L;

    @Override
    public boolean onSave(Object o, Serializable id, Object[] state,
            String[] propertyNames, Type[] types) {

        AbstractEntity entity = (AbstractEntity)o;
        boolean isNew = (entity.getId() == null);
        Date now = new Date();
        
        for(int i = 0; i < propertyNames.length; i++) {
            String propertyName = propertyNames[i];
            if("active".equals(propertyName) && isNew) {
                state[i] = true;
            } else if("updated".equals(propertyName) || ("created".equals(propertyName) && isNew)) {
                state[i] = now;
            }
        }
        
        return true;
    }
    
}