from zeep import client

wsdl_url = 'http://www.dneonline.com/calculator.asmx?WSDL'
client = Client(wsdl=wsdl_url)
resultado = client.service.Add(intA=5, intB=3)
print(f"El resultado de la suma es: {resultado}")
