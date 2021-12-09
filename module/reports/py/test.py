import requests
import json

# url = "http://127.0.0.1:3000/render/d-solo/szlpQ4t7k/localhost-dashboard?orgId=1&from=1637498496329&to=1637671296329&panelId=2&width=1000&height=500&tz=Europe%2FParis"
# x = requests.get(url, verify=False)                                                   1637498496329
# file = open("ressources/sample_image.png", "wb")
# file.write(x.content)
# file.close()
# print(x)



def grafanaGraph(dashId, dashName, period, serviceId):
    per1 = int(period[0]) * 1000
    per2 = int(period[1]) * 1000

    url = "http://127.0.0.1:3000/render/d-solo/" + dashId + "/" + dashName + "-dashboard?orgId=1&from=" + str(per1) + "&to=" + str(per2) + "&panelId=" + serviceId + "&width=1000&height=500&tz=Europe%2FParis"
    print(url)
    x = requests.get(url, verify=False)
    file = open("ressources/" + dashId + "_" + serviceId + ".png", "wb")
    file.write(x.content)
    file.close()

# grafanaGraph("szlpQ4t7k", "localhost", ["1637498496", "1637671296"], "1")
