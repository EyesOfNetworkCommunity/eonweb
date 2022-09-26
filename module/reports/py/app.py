# -*- coding: utf-8 -*-
from flask import request, Flask
from xvfbwrapper import Xvfb
import sla
import graf
import jinja2
import pdfkit
import os

app = Flask(__name__)

@app.route("/report", methods=["POST"])
def create_report():
    reportname = request.json['reportname']
    equipement = request.json['equipement']
    period = request.json['period']
    type = request.json['type']
    key = request.json["key"]
    eDash = []

    for i, dash in enumerate(equipement):
        eDash.append({})
        eDash[i]["hostname"] = dash["name"]
        eDash[i]["dash_url"] = {}
        for service in dash["dashPanelId"]:
            graf.grafanaGraph(dash["dashId"], dash["name"], period, service)
            eDash[i]["dash_url"][service] = "../" + dash["dashId"] + "_" + service + ".png"

        filters = ["host_name = " + dash["name"], "time > " + period[0], "time < " + period[1]]
        sla.renderPlotPng(filters, dash["dashId"], type, key)
        eDash[i]["sla_url"] = "../" + dash["dashId"] + "_sla.png"
    
    templateLoader = jinja2.FileSystemLoader(searchpath="/srv/eyesofnetwork/eonweb/module/reports/py/templates/")
    templateEnv = jinja2.Environment(loader=templateLoader)
    TEMPLATE_FILE = "report.html"
    template = templateEnv.get_template(TEMPLATE_FILE)
    
    output = template.render(graph = eDash)
    report_html_path = '/srv/eyesofnetwork/eonweb/module/reports/py/ressources/reports/' + reportname + '_report.html'
    html_file = open(report_html_path, 'w')
    html_file.write(output)
    html_file.close()

    # OPTIONS
    options = {
        'page-size':'A4',
        'encoding':'utf-8', 
        'margin-top':'0cm',
        'margin-bottom':'0cm',
        'margin-left':'0cm',
        'margin-right': '0cm'
    }

    vdisplay = Xvfb()
    vdisplay.start()
    # transform html to pdf with pdfkit
    pdfkit.from_file(report_html_path, '/srv/eyesofnetwork/eonweb/module/reports/py/ressources/reports/' + reportname + '_report.pdf', options=options, verbose=True)
    vdisplay.stop()

    if os.path.exists(os.getcwd() + "/" + report_html_path):
        os.remove(os.getcwd() + "/" + report_html_path)

    for i, da in enumerate(eDash):
        if os.path.exists(os.getcwd() + "/srv/eyesofnetwork/eonweb/module/reports/py/ressources/reports/" + da["sla_url"]):
            os.remove(os.getcwd() + "/srv/eyesofnetwork/eonweb/module/reports/py/ressources/reports/" + da["sla_url"])
        for url in da["dash_url"].items(): 
            if os.path.exists(os.getcwd() + "/srv/eyesofnetwork/eonweb/module/reports/py/ressources/reports/" + url[1]):
                os.remove(os.getcwd() + "/srv/eyesofnetwork/eonweb/module/reports/py/ressources/reports/" + url[1])
    return "Report generated"

if __name__ == "__main__":
    app.run(host='127.0.0.1')
    app.run(debug=True)
    