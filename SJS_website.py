#!/usr/bin/python

# Written by Phil Tanner, 2014-04-08, based on information from
# http://stockrt.github.io/p/emulating-a-browser-in-python-with-mechanize/

# Idea is to automate the gathering of data as CSV via the built-in website reports

import re
import mechanize
import argparse
import time
import sys
import urllib
import datetime
import uuid
import os
import json

#socket.setdefaulttimeout(15000)
mechanize._sockettimeout._GLOBAL_DEFAULT_TIMEOUT = 100

cookiepathname = '/tmp/'

def Browser( uid = "" ):
  "A standard SJS Python browser object"
  # Initialise our browser object
  br = mechanize.Browser(factory=mechanize.RobustFactory())

  cookiejar = mechanize.MozillaCookieJar()
  if uid != "":
    if os.path.isfile(cookiepathname+uid+'.cookie'):
      cookiejar.load(cookiepathname+uid+'.cookie')

#  print(json.dumps(cookiejar.cookie))

  br.set_cookiejar(cookiejar)

  br.addheaders = [('User-agent', 'SJS Python Mechanize browser, version 0.3')]
  #br.addheaders = [('User-agent', 'Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.0.6')]
  br.set_handle_robots(False)
  
  return(br)

 
def Login( username, password, redirect = "", session = "", uid = "" ):
  "Perform a login on the SJS browser and return the session object of the requested logged in page"

  #if uid == "":
  #  uid = str(uuid.uuid4())
	
  if session == "":
    session = Browser( uid )

  loginURL = 'http://sjs.co.nz/user/login'
  if redirect != "":
    loginURL = loginURL + '?redirect=' + redirect

  session.open(loginURL)
  
  # Find the login form by ID (it doesn't have a name attribute, which would have been much easier!)
  formcount=0
  for frm in session.forms():  
    if str(frm.attrs["id"])=='user-login':
      break
    formcount=formcount+1

  try:
    session.select_form(nr=formcount)

    #This would have been nicer :)
    #br.select_form(name='user-login') 

    session['name'] = username
    session['pass'] = password

    # Submit our form, so we can be logged in
    response = session.submit()
    res = response.read()

    # We should see this message if it worked...
    if re.search('You are logged in', res):
      # Save our session cookies for next time, if we have a UUID to do so
      if uid != "":
        session._ua_handlers['_cookies'].cookiejar.save(cookiepathname+uid+'.cookie')
    else:
      sys.stderr.write("SJS_website:Login:Failed to login successfully. Invalid credentials? \n")
      sys.exit(1)
    
  # This means the page didn't have the login form on it. 
  # Which means we're already logged in(?) so we can access the page directly instead
  except mechanize._mechanize.FormNotFoundError:
    session.open('http://sjs.co.nz/'+redirect)
    pass
  
  return(session)


def Logout( session = "", uid = "" ):
  "Perform a login on the SJS browser and return the session object of the requested logged in page"

  #if uid == "":
  #  uid = str(uuid.uuid4())

  if session == "":
    session = Browser( uid )

  response = session.open('http://sjs.co.nz/user/logout')

  res = response.read()

  return(session)

