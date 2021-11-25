# -*- coding: utf-8 -*-
from flask import request, Flask
import sla
import test
import jinja2
import json

app = Flask(__name__)

@app.route("/report", methods=["POST"])
def create_report():
    if not request.json or not 'hostname' in request.json or not 'period' in request.json:
        return "not ok"
    # filters = ["host_name = " + request.json['hostname'], "time > " + request.json['period'][0], "time < " + request.json['period'][1]]
    hostname = request.json['hostname']
    dashId = request.json['dashId']
    serviceId = request.json['serviceId']
    period = request.json['period']
    # sla.renderPlotPng(filters, dashId, serviceId)
    graphs = []
    slaGraphs = []
    dada = []
    for i, dash in enumerate(dashId):
        # dada[i] = hostname[i]
        dada.append({})
        dada[i]["hostname"] = hostname[i]
        dada[i]["dash_url"] = {}
        for service in serviceId:
            test.grafanaGraph(dash, hostname[i], period, service)
            # graphs.append("ressources/" + dash + "_" + service + ".png")
            dada[i]["dash_url"][service] = "ressources/" + dash + "_" + service + ".png"

        filters = ["host_name = " + hostname[i], "time > " + period[0], "time < " + period[1]]
        sla.renderPlotPng(filters, dash)
        dada[i]["sla_url"] = "ressources/" + dash + "_sla.png"
        # slaGraphs.append("ressources/" + dash + "_sla.png")


    templateLoader = jinja2.FileSystemLoader(searchpath="./templates/")
    templateEnv = jinja2.Environment(loader=templateLoader)
    TEMPLATE_FILE = "report_cogouv.html"
    template = templateEnv.get_template(TEMPLATE_FILE)
    
    output = template.render(sla=slaGraphs, graph = dada)
    html_file = open('rapport.html', 'w')
    html_file.write(output)
    html_file.close()
    return "Ok"

if __name__ == "__main__":
    app.run(host='0.0.0.0', debug=True)
