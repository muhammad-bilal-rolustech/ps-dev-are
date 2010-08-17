<?php

$schedule_xml = <<<EXM
<?xml version="1.0" encoding="ISO-8859-1"?>
<serv:message xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
   <header>
 <securityContext>
      <webExID></webExID>
      <password></password>
      <siteID></siteID>
      <siteName></siteName>
      <partnerID></partnerID>
    </securityContext>
   </header>
   <body>
      <bodyContent
          xsi:type="java:com.webex.service.binding.meeting.CreateMeeting">
         <accessControl>
            <meetingPassword></meetingPassword>
         </accessControl>
         <metaData>
            <confName></confName>
            <meetingType>0</meetingType>
            <agenda></agenda>
         </metaData>
         <participants>
            <maxUserNumber>0</maxUserNumber>
            <attendees>
            </attendees>
         </participants>
         <enableOptions>
            <chat>true</chat>
            <poll>true</poll>
            <audioVideo>true</audioVideo>
         </enableOptions>
         <schedule>
            <startDate></startDate>
            <openTime></openTime>
            <joinTeleconfBeforeHost>true</joinTeleconfBeforeHost>
            <duration></duration>
            <timeZoneID></timeZoneID>
         </schedule>
         <telephony>
            <telephonySupport>CALLIN</telephonySupport>
            <extTelephonyDescription>
            </extTelephonyDescription>
         </telephony>
      </bodyContent>
   </body>
</serv:message>
EXM;

$unschedule_xml = <<<UNS
<?xml version="1.0" encoding="ISO-8859-1"?>
<serv:message xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
   <header>
       <securityContext>
      <webExID></webExID>
      <password></password>
      <siteID></siteID>
      <siteName></siteName>
      <partnerID></partnerID>
    </securityContext>
   </header>
   <body>
      <bodyContent
          xsi:type="java:com.webex.service.binding.meeting.DelMeeting">
         <meetingKey></meetingKey>
      </bodyContent>
   </body>
</serv:message>
UNS;

$invite_xml = <<<INV
<?xml version="1.0"?>
<serv:message xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
   <header>
       <securityContext>
         <webExID></webExID>
         <password></password>
         <siteID></siteID>
         <siteName></siteName>
         <partnerID></partnerID>
    </securityContext>
   </header>
   <body>
      <bodyContent xsi:type=
          "java:com.webex.service.binding.attendee.CreateMeetingAttendee">
     </bodyContent>
   </body>
</serv:message>
INV;

$uninvite_xml = <<<UNI
<?xml version="1.0" encoding="ISO-8859-1"?>
<serv:message xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
   <header>
      <securityContext>
         <webExID></webExID>
         <password></password>
         <siteID></siteID>
         <siteName></siteName>
         <partnerID></partnerID>
      </securityContext>
   </header>
   <body>
      <bodyContent
          xsi:type="java:com.webex.service.binding.attendee.DelMeetingAttendee">
         <attendeeID></attendeeID>
      </bodyContent>
   </body>
</serv:message>
UNI;

$details_xml = <<<DTL
<serv:message xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
   <header>
      <securityContext>
         <webExID></webExID>
         <password></password>
         <siteID></siteID>
         <siteName></siteName>
         <partnerID></partnerID>
      </securityContext>
   </header>
   <body>
      <bodyContent xsi:type="java:com.webex.service.binding.meeting.GetMeeting">
         <meetingKey></meetingKey>
      </bodyContent>
   </body>
</serv:message>
DTL;

$listmeeting_xml = <<<LST
<?xml version="1.0" encoding="ISO-8859-1"?>
<serv:message xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
   <header>
      <securityContext>
         <webExID></webExID>
         <password></password>
         <siteID></siteID>
         <siteName></siteName>
         <partnerID></partnerID>
      </securityContext>
   </header>
   <body>
      <bodyContent
          xsi:type="java:com.webex.service.binding.meeting.LstsummaryMeeting">
         <listControl>
            <startFrom>1</startFrom>
            <maximumNum></maximumNum>
            <listMethod>OR</listMethod>
         </listControl>
         <order>
            <orderBy>HOSTWEBEXID</orderBy>
            <orderAD>ASC</orderAD>
            <orderBy>CONFNAME</orderBy>
            <orderAD>ASC</orderAD>
            <orderBy>STARTTIME</orderBy>
            <orderAD>ASC</orderAD>
         </order>
      </bodyContent>
   </body>
</serv:message>
LST;

$joinmeeting_xml = <<<JMT
<?xml version="1.0" encoding="ISO-8859-1"?>
<serv:message xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
   <header>
      <securityContext>
         <webExID></webExID>
         <password></password>
         <siteID></siteID>
         <siteName></siteName>
         <partnerID></partnerID>
      </securityContext>
   </header>
   <body>
      <bodyContent
          xsi:type="java:com.webex.service.binding.meeting.GetjoinurlMeeting">
         <sessionKey></sessionKey>
         <attendeeName></attendeeName>
      </bodyContent>
   </body>
</serv:message>
JMT;

$hostmeeting_xml = <<<HST
<?xml version="1.0" encoding="ISO-8859-1"?>
<serv:message xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
   <header>
      <securityContext>
         <webExID></webExID>
         <password></password>
         <siteID></siteID>
         <siteName></siteName>
         <partnerID></partnerID>
      </securityContext>
   </header>
   <body>
      <bodyContent
          xsi:type="java:com.webex.service.binding.meeting.GethosturlMeeting">
         <sessionKey></sessionKey>
      </bodyContent>
   </body>
</serv:message>
HST;
