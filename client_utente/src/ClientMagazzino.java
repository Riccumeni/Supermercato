import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.net.HttpURLConnection;
import java.net.URL;
import java.nio.Buffer;

import org.json.simple.JSONObject;
import org.json.simple.parser.JSONParser;
import org.json.simple.parser.ParseException;

public class ClientMagazzino {
    static BufferedReader tastiera = new BufferedReader(new InputStreamReader(System.in));
    final static String indirizzo = "http://riccumeni.altervista.org/Supermercato/api";
    public static void main(String[] args) throws java.io.IOException, ParseException{

        JSONObject body = new JSONObject();
        JSONParser parser = new JSONParser();
        BufferedReader tastiera = new BufferedReader(new InputStreamReader(System.in));
        
        String scelta = "";
        int codiceUtente = -1;

        while(!scelta.equals("exit")){
            System.out.println("Selezionare un operazione:");
            System.out.println("1. Login\n2. Registrazione\n3. Password Dimenticata\nexit. Esci");
            scelta = tastiera.readLine();
            switch(scelta){
                case "1":
                codiceUtente = login();
                if(codiceUtente!=-1){
                    body = new JSONObject(); // pulisco il body
                    while(!scelta.equals("indietro")){
                        System.out.println("Scegliere una delle seguenti operazioni:");
                        System.out.println("1. Ricerca\n2. Carrello\n3. Modifica Password\nindietro. Torna indietro");
                        scelta = tastiera.readLine();
                        switch(scelta){
                            case "1":
                            System.out.println("1. Per nome\n2. Per categoria");
                            scelta = tastiera.readLine();
                            switch(scelta){
                                case "1":
                                ricercaProdottoNome(codiceUtente);
                                break;
                                case "2":
                                visualizzaCategorie();
                                ricercaProdottoCategoria(codiceUtente);
                                break;
                            }
                            break;
                            case "2":
                            String s = getCarrello(codiceUtente);
                            System.out.println(s);
                            System.out.println("Operazioni disponibili:");
                            System.out.println("1. Rimuovi Elemento\n2. Ordina");
                            s = tastiera.readLine();
                            switch(s){
                                case "1":
                                rimuoviElemento(codiceUtente);
                                break;
                                case "2":
                                ordina(codiceUtente);
                                break;
                            }
                            break;
                            case "3":
                            recuperoPassword();
                            break;
                        }
                    }
                }
                break;
                case "2":
                registrazione();
                break;
                case "3":
                recuperoPassword();

                break;
                case "exit":
                break;
            }
        }

    }

    public static JSONObject postRequest(String indirizzo,String bodyCarrello) throws ParseException{
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

        
        return (JSONObject) parser.parse(risposta);
    }

    public static int login(){

        JSONObject body = new JSONObject();
        JSONParser parser = new JSONParser();
        BufferedReader tastiera = new BufferedReader(new InputStreamReader(System.in));      
        String s = "";
        int codiceUtente = -1;
        
        try {
            System.out.println("Inserisci l'email:");
            s=tastiera.readLine();
            body.put("email", s);

            System.out.println("Inserisci la password:");
            s=tastiera.readLine();
            body.put("password", s);
        } catch (Exception e) {
            e.printStackTrace();
        }
        
        try {
            JSONObject response = postRequest(indirizzo + "/accesso/login.php", body.toJSONString());
            if((Boolean)response.get("success")){
                System.out.println(response.get("message"));
                codiceUtente = Integer.parseInt((String)response.get("id"));
                
            }else{
                System.out.println(response.get("message"));
            }
        } catch (ParseException e) {
            e.printStackTrace();
        }

        return codiceUtente;
    }

    public static void recuperoPassword(){
        JSONObject body = new JSONObject();
        try{
            System.out.println("Inserire l'email:");
            String s = tastiera.readLine();
            body.put("email", s);
            try {
                JSONObject response = postRequest(indirizzo + "/accesso/password/recupero.php", body.toJSONString());
                if((Boolean) response.get("success")){
                    System.out.println(response.get("message"));
                }else{
                    System.out.println(response.get("message"));
                }
            } catch (ParseException e) {
                // TODO Auto-generated catch block
                e.printStackTrace();
            }
        }catch(Exception e){
            
        }  
    }

    public static void rimuoviElemento(int codiceUtente){
        JSONObject bodyCarrello = new JSONObject();
        try {
            System.out.println("Selezionare il codice del prodotto:");
            String s = tastiera.readLine();
            bodyCarrello.put("codiceProdotto", s);
            System.out.println("Inserire la quantita da rimuovere: (0 per eliminarlo)");
            s = tastiera.readLine();
            bodyCarrello.put("quantita", s);
            bodyCarrello.put("codiceUtente", codiceUtente);

            JSONObject response = postRequest(indirizzo + "/carrello/rimuovi.php", bodyCarrello.toJSONString());
            System.out.println(response.get("message"));
        } catch (Exception e) {
            //TODO: handle exception
        }
        
    }

    public static String getCarrello(int codiceUtente){
        String s = "";
        try {
            System.out.println("------ Carrello ------");
            JSONObject bodyCarrello = new JSONObject();
            bodyCarrello.put("codice", codiceUtente);
            JSONObject carrello = postRequest(indirizzo + "/carrello/getall.php", bodyCarrello.toJSONString());
            System.out.println(carrello.get("data"));
            System.out.println("Inserire un operazione:");
            System.out.println("1. Rimuovi Elemento\n2. Ordina");
            s = tastiera.readLine();
        } catch (Exception e) {
            //TODO: handle exception
        }
        return s;
    }

    public static void registrazione(){
        JSONObject body = new JSONObject();
        try {
            System.out.println("Inserire l'email");
            String s = tastiera.readLine();
            body.put("email", s);
            System.out.println("Inserire la password");
            s = tastiera.readLine();
            body.put("password", s);

            try {
                JSONObject response = postRequest(indirizzo + "/accesso/registrazione.php", body.toJSONString());
                if((Boolean) response.get("success")){
                    System.out.println(response.get("message"));
                }else{
                    System.out.println(response.get("message"));
                }
            } catch (ParseException e) {
                e.printStackTrace();
            }
        } catch (Exception e) {
            //TODO: handle exception
        }
    }

    public static void ricercaProdottoCategoria(int codiceUtente){
        JSONObject body = new JSONObject();
        try {
            System.out.println("Inserisci il genere del prodotto da cercare");
            String nome=tastiera.readLine();
            body.put("prodotto",nome);
            JSONObject response = postRequest(indirizzo + "/ricerca/genere.php", body.toJSONString());

            if((Boolean) response.get("success")){
                body = new JSONObject();
                System.out.println(response.get("data"));
                System.out.println("Inserire il codice del prodotto: (-1 se non si desidera niente)");
                String s = tastiera.readLine();
                if(!s.equals("-1")){
                    body.put("codiceProdotto", s);
                    System.out.println("Selezionare la quantita");
                    s = tastiera.readLine();
                    body.put("quantita", s);
                    body.put("codiceUtente", codiceUtente);
                    response = postRequest(indirizzo + "/carrello/aggiungi.php", body.toJSONString());
                    System.out.println(response.get("message"));
                }  

            }else{
                System.out.println(response.get("message"));
            }
        } catch (Exception e) {
            //TODO: handle exception
        }
    }

    public static void ricercaProdottoNome(int codiceUtente){
        JSONObject body = new JSONObject();
        try {
            System.out.println("Inserisci il nome del prodotto da cercare");
            String nome=tastiera.readLine();
            body.put("nome",nome);
            JSONObject response = postRequest(indirizzo + "/ricerca/nome.php", body.toJSONString());

            if((Boolean) response.get("success")){
                body = new JSONObject();
                System.out.println(response.get("data"));
                System.out.println("Inserire il codice del prodotto: (-1 se non si desidera niente)");
                String s = tastiera.readLine();
                if(!s.equals("-1")){
                    body.put("codiceProdotto", s);
                    System.out.println("Selezionare la quantita");
                    s = tastiera.readLine();
                    body.put("quantita", s);
                    body.put("codiceUtente", codiceUtente);
                    response = postRequest(indirizzo + "/carrello/aggiungi.php", body.toJSONString());
                    System.out.println(response.get("message"));
                }  

            }else{
                System.out.println(response.get("message"));
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    public static void ordina(int codiceUtente){
        JSONObject body = new JSONObject();
        try {
            body.put("id", codiceUtente);
            JSONObject response = postRequest(indirizzo + "/ordina/utente.php", body.toJSONString());

            if((Boolean) response.get("success")){
                System.out.println(response.get("data"));
            }else{
                System.out.println(response.get("message"));
            }
        } catch (Exception e) {
            //TODO: handle exception
        }
    }
    
    public static void visualizzaCategorie(){
        try {
            JSONObject response = postRequest(indirizzo + "/categoria/getall.php", "");
            if((Boolean) response.get("success")){
                System.out.println("--- CATEGORIE ---");
                System.out.println(response.get("data"));
            }else{
                System.out.println(response.get("message"));
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
    }    
}