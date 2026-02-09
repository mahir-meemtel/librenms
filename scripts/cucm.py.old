#!/usr/bin/env python3

"""

CUCM RISService70 Device Registration Status

Uses raw SOAP over HTTPS (curl-equivalent)

"""



import sys

import argparse

import requests

import xml.etree.ElementTree as ET

import urllib3



urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)



SOAP_TEMPLATE = """<?xml version="1.0" encoding="UTF-8"?>

<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"

                  xmlns:tns="http://schemas.cisco.com/ast/soap"

                  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">

  <soapenv:Header/>

  <soapenv:Body>

    <tns:selectCmDevice>

      <tns:StateInfo></tns:StateInfo>

      <tns:CmSelectionCriteria>

        <tns:MaxReturnedDevices>1000</tns:MaxReturnedDevices>

        <tns:DeviceClass>Phone</tns:DeviceClass>

        <tns:Model>255</tns:Model>

        <tns:Status>Any</tns:Status>

        <tns:NodeName xsi:nil="true"/>

        <tns:SelectBy>Name</tns:SelectBy>

        <tns:SelectItems>

          <tns:item>

            <tns:Item>*</tns:Item>

          </tns:item>

        </tns:SelectItems>

        <tns:Protocol>Any</tns:Protocol>

        <tns:DownloadStatus>Any</tns:DownloadStatus>

      </tns:CmSelectionCriteria>

    </tns:selectCmDevice>

  </soapenv:Body>

</soapenv:Envelope>

"""



NS = {

    "soap": "http://schemas.xmlsoap.org/soap/envelope/",

    "c": "http://schemas.cisco.com/ast/soap",

}



def get_device_status(cucm_host, username, password):

    url = f"https://{cucm_host}:8443/realtimeservice2/services/RISService70"



    headers = {

        "Content-Type": "text/xml; charset=utf-8",

        "SOAPAction": "selectCmDevice",

    }



    response = requests.post(

        url,

        data=SOAP_TEMPLATE,

        headers=headers,

        auth=(username, password),

        verify=False,

        timeout=30,

    )



    if response.status_code != 200:

        print(f"ERROR: HTTP {response.status_code}", file=sys.stderr)

        sys.exit(1)



    try:

        root = ET.fromstring(response.text)

    except ET.ParseError as e:

        print(f"XML parse error: {e}", file=sys.stderr)

        sys.exit(1)



    print("DeviceName,Status,IP,Model,DirNumber,Node,Description")



    total_devices = 0



    for node in root.findall(".//c:CmNodes/c:item", NS):

        node_name = node.findtext("c:Name", default="", namespaces=NS)



        devices = node.find("c:CmDevices", NS)

        if devices is None:

            continue



        for dev in devices.findall("c:item", NS):

            total_devices += 1



            name = dev.findtext("c:Name", default="", namespaces=NS)

            status = dev.findtext("c:Status", default="Unknown", namespaces=NS)

            dirnum = dev.findtext("c:DirNumber", default="", namespaces=NS)

            model = dev.findtext("c:Model", default="0", namespaces=NS)

            desc = dev.findtext("c:Description", default="", namespaces=NS)



            ip = ""

            ip_items = dev.find("c:IPAddress", NS)

            if ip_items is not None:

                ip_item = ip_items.find("c:item", NS)

                if ip_item is not None:

                    ip = ip_item.findtext("c:IP", default="", namespaces=NS)



            print(f'"{name}","{status}","{ip}",{model},"{dirnum}","{node_name}","{desc}"')



    print(f"\nTotal devices found: {total_devices}", file=sys.stderr)





if __name__ == "__main__":

    parser = argparse.ArgumentParser(description="CUCM RIS Device Status (SOAP)")

    parser.add_argument("cucm", help="CUCM IP/hostname")

    parser.add_argument("-u", "--user", default="risuser", help="Username")

    parser.add_argument("-p", "--password", default="simplepass123", help="Password")



    args = parser.parse_args()



    get_device_status(args.cucm, args.user, args.password)
