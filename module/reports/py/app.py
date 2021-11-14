# -*- coding: utf-8 -*-
from flask import request, Flask
import sla
import jinja2
import json

app = Flask(__name__)

@app.route("/report", methods=["POST"])
def create_report():
    if not request.json or not 'hostname' in request.json or not 'period' in request.json:
        return "not ok"
    filters = ["host_name = " + request.json['hostname'], "time > " + request.json['period'][0], "time < " + request.json['period'][1]]
    sla.renderPlotPng(filters)
    templateLoader = jinja2.FileSystemLoader(searchpath="./templates/")
    templateEnv = jinja2.Environment(loader=templateLoader)
    TEMPLATE_FILE = "report.html"
    template = templateEnv.get_template(TEMPLATE_FILE)
    plot_url = "sla.png"
    output = template.render(plot_url=plot_url)
    html_file = open('rapport.html', 'w')
    html_file.write(output)
    html_file.close()
    return "Ok"

if __name__ == "__main__":
    app.run(host='0.0.0.0', debug=True)