import requests

def grafanaGraph(dashId, dashName, period, serviceId):
    per1 = int(period[0]) * 1000
    per2 = int(period[1]) * 1000
    
    url = "http://127.0.0.1:3000/render/d-solo/" + dashId + "/" + dashName + "-dashboard?orgId=1&from=" + str(per1) + "&to=" + str(per2) + "&panelId=" + serviceId + "&width=1060&height=550&tz=Europe%2FParis"
    x = requests.get(url, verify=False)
    file = open("/srv/eyesofnetwork/eonweb/module/reports/py/ressources/" + dashId + "_" + serviceId + ".png", "wb")
    file.write(x.content)
    file.close()
