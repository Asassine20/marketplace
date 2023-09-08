#Code for creating users and authentication. MAY NEED TO UPDATE WITH A DIFFERENT SERVER SUCH AS GUNICORN OR uWSGI


from flask import Flask, render_template, redirect, url_for, flash
from flask_wtf import FlaskForm
from wtforms import StringField, PasswordField, SubmitField
from wtforms.validators import DataRequired, Email, EqualTo
from flask_login import LoginManager, UserMixin, login_user, login_required, logout_user, current_user
import bcrypt
import pymysql
from connect import get_connection

app = Flask(__name__)
app.config['SECRET KEY'] = 'your_secret_key'

#Initialize Flask-Login
login_manager = LoginManager(app)
login_manager.login_view = 'login'

# Establish a connection
db = get_connection()

#Example user class
class User(UserMixin):
  def __init__(self, user_id, username, email):
    self.id = user_id
    self.username = username
    self.email = email
  
#User loader function for flask-login
@login_manager.user_loader
def load_user(user_id):
  cursor = db.cursor()
  cursor.execute("SELECT user_id, username, email FROM users WHERE user_id = %s", (user_id,))
  user_data = cursor.fetchone()
  cursor.close()
  if user_data:
    user_id, username, email = user_data
    return User(user_id, username, email)
  return None

#Login form Flask-WTF
class LoginForm(FlaskForm):
  email = StringField('Email', validators = [DataRequired(), Email()])
  password = PasswordField('Password', validators = [DataRequired()])
  submit = SubmitField('Login')
  
#Registration form using Flask-WTF
class RegistrationForm(FlaskForm):
  email = StringField('Email', validators = [DataRequired(), Email()])
  password = PasswordField('Password', validators = [DataRequired()])
  confirm_password = PasswordField('Confirm Password', validators = [DataRequired(), EqualTo('password')])
  submit = SubmitField('Register')
  
#Routes for login, logout, and registration
@app.route('/login', methods = ['GET', 'POST'])
def login():
  form = LoginForm()
  if form.validate_on_submit():
    email = form.email.data 
    password = form.password.data.encode('utf-8')
    
    cursor = db.cursor()
    cursor.execute("SELECT user_id, username, password_hash FROM users WHERE email = %s", (email,))
    user_data = cursor.fetchone()
    cursor.close()
    
    if user_data:
      user_id, username, hashed_password = user_data
      if bcrypt.checkpw(password, hashed_password.encode('utf-8')):
        user_obj = User(user_id, username, email)
        login_user(user_obj)
        flash('Logged in successfully.', 'success')
        return redirect(url_for('dashboard'))
      flash('Invalid email or password. Please try again.', 'danger')
    return render_template('login.html', form=form)
  
@app.route('/logout')
@login_required
def logout():
  logout_user()
  flash('Logged out successfully', 'success')
  return redirect(url_for('login'))

@app.route('/register', methods = ['GET', 'POST'])
def register():
  form = RegistrationForm()
  if form.validate_on_submit():
    email = form.email.data 
    password = form.password.data.encode('utf-8')
    
    cursor = db.cursor()
    cursor.execute("SELECT user_id FROM users WHERE emaill = %s", (email,))
    if cursor.fethcone():
      cursor.close()
      flash('Email is already registered', 'danger')
      return redirect(url_for('register'))
    
    hashed_password = bcrypt.haspw(password, bcrypt.gensalt())
    
    cursor.execute("INSERT INTO users (email, password) VALUES (%s, %s)", (email, hashed_password))
    db.commit()
    cursor.close()
    
    flash('Registration successful. Please login.', 'success')
    return redirect(url_for('login'))
  return render_template('register.html', form=form)


@app.route('/dashboard')
@login_required
def dashboard():
  return render_template('dashboard.html', user = current_user)


if __name__ == '__main__':
  app.run(debug = True)
  
    
