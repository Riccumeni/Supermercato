import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.net.HttpURLConnection;
import java.net.URL;

import org.json.simple.JSONObject;
import org.json.simple.parser.JSONParser;

/*
* -- FUNZIONALITA --
* login
* incassi giornalieri, mensili, annuali
* Visualizza tutti i fornitori
* Aggiungi un fornitore
* Modificare l'email a un fornitore
* Mandare una mail al fornitore
*/
public class App {
    static BufferedReader tastiera = new BufferedReader(new InputStreamReader(System.in));
    static final String indirizzo = "http://localhost/Supermercato/api";
    public static void main(String[] args) throws Exception {
        String scelta = "";
        while(!scelta.equals("fine")){
            System.out.println("Seleziona una delle seguenti operazioni:");
            System.out.println("1. Login\nfine. Esci");
            scelta = tastiera.readLine();
            if(scelta.equals("1")){
                int codiceUtente = login();
                if(codiceUtente>-1){
                    while(!scelta.equals("indietro")){
                        System.out.println("Selezionare una delle seguenti operazioni:");
                        System.out.println("1. Guarda Incassi\n2. Gestione Fornitori\nindietro. Torna indietro");
                        scelta = tastiera.readLine();
                        switch(scelta){
                            case "1":
                            break;
                            case "2":
                            break;
                        }
                    }
                    
                }
            }
        }
    }

    public static JSONObject postRequest(String indirizzo,String bodyCarrello){
        String risposta = null;
        JSONParser parser = new JSONParser();
        try{
            URL uri = new URL(indirizzo);
            HttpURLConnection con = (HttpURLConnection) uri.openConnection();
            con.setRequestMethod("POST");
            con.setDoInput(true);
            con.setDoOutput(true);
            OutputStream os = con.getOutputStream();
            os.write(bodyCarrello.getBytes());
            os.flush();
            os.close();

            int rCode = con.getResponseCode();
            if(rCode == 200){
                BufferedReader in = new BufferedReader(new InputStreamReader(con.getInputStream()));
                String input = "";
                while((input = in.readLine()) != null){
                    risposta = input;
                }
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
        
        
        return (JSONObject) parser.parse(risposta) ;
    }

    public static int login(){
        String s = "";
        JSONObject body = new JSONObject();
        int codiceUtente = -1;
        try {
            System.out.println("Inserire l'email:");
            s = tastiera.readLine();
            body.put("email", s);
            System.out.println("Inserire la password:");
            s = tastiera.readLine();
            body.put("password", s);

            body = postRequest(indirizzo + "/accesso/loginAmministratore.php", body.toJSONString());

            if((Boolean) body.get("success")){
                codiceUtente = Integer.parseInt((String)body.get("id"));
                System.out.println(body.get("message"));
            }else{
                System.out.println(body.get("message"));
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
        return codiceUtente;
    }

    public static void incassiGiornalieri(){}

    public static void incassiMensili(){}
    
    public static void incassiAnnuali(){}

    public static void aggiungiFornitore(){}

    public static void modificaEmail(){}

    public static void mandaEmail(){}
}
