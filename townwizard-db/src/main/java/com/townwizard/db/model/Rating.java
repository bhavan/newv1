package com.townwizard.db.model;

import javax.persistence.Entity;
import javax.persistence.FetchType;
import javax.persistence.JoinColumn;
import javax.persistence.OneToOne;

@Entity
public class Rating extends AuditableEntity {
    
    private static final long serialVersionUID = 5715292325466814193L;

    @OneToOne(fetch = FetchType.LAZY) @JoinColumn(name = "userId")
    private User user;
    @OneToOne(fetch = FetchType.LAZY) @JoinColumn(name = "contentId")
    private Content content;
    private Float value;
    
    public User getUser() {
        return user;
    }
    public void setUser(User user) {
        this.user = user;
    }
    public Content getContent() {
        return content;
    }
    public void setContent(Content content) {
        this.content = content;
    }
    public Float getValue() {
        return value;
    }
    public void setValue(Float value) {
        this.value = value;
    }

}
