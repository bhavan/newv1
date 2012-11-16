package com.townwizard.db.model;

import java.util.HashMap;
import java.util.Map;

import javax.persistence.Entity;
import javax.persistence.JoinColumn;
import javax.persistence.OneToOne;

import org.codehaus.jackson.annotate.JsonIgnore;

@Entity
public class Address extends AuditableEntity {
    
    private static final long serialVersionUID = -7937478988122266498L;
    
    private String address1;
    private String address2;
    private String city;
    private String state;
    private String postalCode;
    private String country;
    @OneToOne @JoinColumn(name = "userId")
    private User user;
    
    public String getAddress1() {
        return address1;
    }
    public void setAddress1(String address1) {
        this.address1 = address1;
    }
    public String getAddress2() {
        return address2;
    }
    public void setAddress2(String address2) {
        this.address2 = address2;
    }
    public String getCity() {
        return city;
    }
    public void setCity(String city) {
        this.city = city;
    }
    public String getState() {
        return state;
    }
    public void setState(String state) {
        this.state = state;
    }
    public String getPostalCode() {
        return postalCode;
    }
    public void setPostalCode(String postalCode) {
        this.postalCode = postalCode;
    }
    public String getCountry() {
        return country;
    }
    public void setCountry(String country) {
        this.country = country;
    }
    
    @JsonIgnore
    public User getUser() {
        return user;
    }
    @JsonIgnore
    public void setUser(User user) {
        this.user = user;
    }
    
    @JsonIgnore
    public boolean isValid() {
       return address1 != null && city != null && state != null && postalCode != null;
    }
    
    public Map<String, String> asParametersMap() {
        Map<String, String> map = new HashMap<>();
        if(getAddress1() != null) map.put("address1", getAddress1());
        if(getAddress2() != null) map.put("address2", getAddress2());
        if(getCity() != null) map.put("city", getCity());
        if(getState() != null) map.put("state", getState());
        if(getPostalCode() != null) map.put("postal_code", getPostalCode());
        if(getCountry() != null) map.put("country", getCountry());
        return map;
    }
    
    @Override
    public String toString() {
        return "Address [address1=" + address1 + ", address2=" + address2
                + ", city=" + city + ", state=" + state + ", postalCode="
                + postalCode + ", country=" + country + "]";
    }

}