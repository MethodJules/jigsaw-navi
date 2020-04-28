
from nlpProcessClass import nlpProcess

# Wird vom Cronjob ausgeführt und instanziiert die Klasse nlpProcess
nlpProc = nlpProcess()
tries = 3

# 3 Versuche für den Verbindungsaufbau zur Datenbank, ansonsten wird die gesamte Aufgabe abgebrochen
for i in range(tries):

    try:
        nlpProc.connect_db()
        nlpProc.add_log("Starting NLP Task")
        nlpProc.start_extraction()
    except Exception as e:
        
        if (type(e).__name__ == "ServiceUnavailable" and i < tries - 1):
            nlpProc.add_log(str(e))
            nlpProc.add_log("Retry connecting")
            continue
        else:
            raise
        
            
    break


            