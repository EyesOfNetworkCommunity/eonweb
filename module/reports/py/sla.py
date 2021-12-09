import requests
import json
import datetime
import pandas as pd
import matplotlib.pyplot as plt

def getStatus(filters, type, key):

    url = "https://localhost/eonapi/listNagiosObjects?username=admin&apiKey=" + key
    myobj = '''{ 
                "object": "log", 
                "columns": ["state","time","host_name"] 
            }'''
            
    myobj = json.loads(myobj)
    myobj["filters"] = filters
    myobj = json.dumps(myobj)

    x = requests.post(url, data = myobj, verify=False).json()
    y = x["result"]["default"]
    date = {}

    if not y:
        return None

    y.reverse()
    result = {
        "Date": [],
        "Sla": []
    }
    for key, val in enumerate(y):
        q = datetime.date.fromtimestamp(val["time"])
        if type == "lastDay" or type == "thisDay" or type == "lastWeek" or type == "thisWeek":
            y[key]["time"] = q.strftime("%Y-%m-%d")
        else:
            y[key]["time"] = q.strftime("%Y-%m")
        if y[key]["time"] not in date:
            date[y[key]["time"]] = []
        else:
            date[y[key]["time"]].append(val["state"])
        
    for key, val in enumerate(date.items()):
        result["Date"].append( val[0])
        result["Sla"].append(val[1].count(0) / len(val[1]) *100)
    return result



def renderPlotPng(filters, dashId, type, key):
    slaMonths = getStatus(filters, type, key)
    if slaMonths == None:
        return None
    df = pd.DataFrame(slaMonths)

    plt.rcParams["figure.figsize"] = (13, 8)
    plt.rcParams.update({'font.size': 13})
    df.plot.bar(x='Date', y='Sla', rot=0)
    plt.savefig("ressources/" + dashId + "_sla.png", bbox_inches='tight')
    plt.close()