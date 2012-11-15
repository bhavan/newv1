package com.townwizard.db.model;

import javax.persistence.CascadeType;
import javax.persistence.Entity;
import javax.persistence.OneToOne;

import com.townwizard.db.util.EmailValidator;

@Entity
public class User extends AuditableEntity {    

    private static final long serialVersionUID = -6562731576094594464L;
    
    private String username;
    private String email;
    private String password;
    private String firstName;
    private String lastName;
    private Integer year;
    private String gender;
    private String mobilePhone;
    private String registrationIp;
    
    @OneToOne(mappedBy = "user", cascade = {CascadeType.ALL})
    private Address address;
    
    public String getUsername() {
        return username;
    }
    public void setUsername(String username) {
        this.username = username;
    }
    public String getEmail() {
        return email;
    }
    public void setEmail(String email) {
        this.email = email;
    }    
    public String getPassword() {
        return password;
    }
    public void setPassword(String password) {
        this.password = password;
    }
    public String getFirstName() {
        return firstName;
    }
    public void setFirstName(String firstName) {
        this.firstName = firstName;
    }
    public String getLastName() {
        return lastName;
    }
    public void setLastName(String lastName) {
        this.lastName = lastName;
    }
    public Address getAddress() {
        return address;
    }
    public void setAddress(Address address) {
        this.address = address;
        if(address != null) {
            address.setUser(this);
        }
    }
    public Integer getYear() {
        return year;
    }
    public void setYear(Integer year) {
        this.year = year;
    }
    public String getGender() {
        return gender;
    }
    public void setGender(String gender) {
        this.gender = gender;
    }
    public String getMobilePhone() {
        return mobilePhone;
    }
    public void setMobilePhone(String mobilePhone) {
        this.mobilePhone = mobilePhone;
    }
    public String getRegistrationIp() {
        return registrationIp;
    }
    public void setRegistrationIp(String registrationIp) {
        this.registrationIp = registrationIp;
    }
    
    public boolean isValid() {
        return isEmailValid() && isPasswordValid();
    }
    
    private boolean isEmailValid() {
        return EmailValidator.isValidEmailAddress(getEmail());
    }
    
    private boolean isPasswordValid() {
        return (password != null && !password.isEmpty());
    }
}