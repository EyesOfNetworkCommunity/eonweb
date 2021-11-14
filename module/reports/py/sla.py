import requests
import json
import datetime
import pandas as pd
import matplotlib.pyplot as plt

def getStatusByMonths(filters):
    # url should not be static
    url = "https://localhost/eonapi/listNagiosObjects?username=admin&apiKey=fc9240d0e5f693322805af565d5e9691bc65676324b83cfb2567b6fc51b92038"

    myobj = '''{ 
                "object": "log", 
                "columns": ["state","time","host_name"] 
            }'''
            
    myobj = json.loads(myobj)
    
    myobj["filters"] = filters
    myobj = json.dumps(myobj)

    x = requests.post(url, data = myobj, verify=False).json()
    date = {
    1 : [],
    2 : [],
    3 : [],
    4 : [],
    5 : [],
    6 : [],
    7 : [],
    8 : [],
    9 : [],
    10 : [],
    11 : [],
    12 : [],
    }

    y = x["result"]["default"]
    for val in y:
        date[datetime.date.fromtimestamp(val["time"]).month].append(val["state"])
    return date

def getSlaByMonths(filters):
    date = {
        "Months" : ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
        "Sla" : []
        }
    status = getStatusByMonths(filters)
    for val in status:
        if len(status[val]) != 0:
            date["Sla"].append(status[val].count(0) / len(status[val]) * 100)
        else:
            date["Sla"].append(0)
    return date

def renderPlotPng(filters):
    slaMonths = getSlaByMonths(filters)
    ss = pd.DataFrame.from_dict(slaMonths)
    ax = ss.plot.bar(x='Months', y='Sla', rot=0)
    plt.savefig("sla.png")