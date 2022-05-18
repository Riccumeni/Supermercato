import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.net.HttpURLConnection;
import java.net.URL;

import org.json.simple.JSONObject;
import org.json.simple.parser.JSONParser;
import org.json.simple.parser.ParseException;

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
                        System.out.println("1. Guarda Incassi\n2. Gestione Fornitori\n3. Inserisci Prodotto\n4. Inserisci Categoria\n5. Visualizza Categorie\n6. Ricerca Prodotto\n7. Guarda Pagamenti\nindietro. Torna indietro");
                        scelta = tastiera.readLine();
                        switch(scelta){
                            case "1":
                            operazioni("/incassi");
                            break;
                            case "2":
                            gestioneFornitori();
                            break;
                            case "3":
                            inserisciProdotto();
                            break;
                            case "4":
                            inserisciCategoria();
                            break;
                            case "5":
                            visualizzaCategorie();
                            break;
                            case "6":
                            ricercaProdottoNome();
                            break;
                            case "7":
                            operazioni("/operazioni");
                            break;
                        }
                    }
                    
                }
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

    public static void operazioni(String indirizzo_sec){
        JSONObject body = new JSONObject();
        try {
            System.out.println("1. Per giorno\n2. Per mese\n3. Per anno");       
            String s = tastiera.readLine();
            switch(s){
                case "1":
                System.out.println("Selezionare il giorno:");
                s = tastiera.readLine();
                body.put("giorno", s);
                System.out.println("Selezionare il mese:");
                s = tastiera.readLine();
                body.put("mese", s);
                System.out.println("Selezionare l'anno");
                s = tastiera.readLine();
                body.put("anno", s);

                body = postRequest(indirizzo + indirizzo_sec +"/getgiorno.php", body.toJSONString());

                if((Boolean) body.get("success")){
                    System.out.println("--- OPERAZIONI ---");
                    System.out.println(body.get("ORDINI"));
                    // System.out.println("TOTALE: " + body.get("INCASSI"));
                }else{
                    System.out.println(body.get("message"));
                }
                break;
                case "2":
                System.out.println("Selezionare il mese:");
                s = tastiera.readLine();
                body.put("mese", s);
                System.out.println("Selezionare l'anno");
                s = tastiera.readLine();
                body.put("anno", s);

                body = postRequest(indirizzo + indirizzo_sec + "getmese.php", body.toJSONString());

                if((Boolean) body.get("success")){
                    System.out.println("--- Ordini ---");
                    System.out.println(body.get("ORDINI"));
                    System.out.println("TOTALE: " + body.get("INCASSI"));
                }else{
                    System.out.println(body.get("message"));
                }
                break;
                case "3":
                System.out.println("Selezionare l'anno");
                s = tastiera.readLine();
                body.put("anno", s);

                body = postRequest(indirizzo + indirizzo_sec + "/getanno.php", body.toJSONString());

                if((Boolean) body.get("success")){
                    System.out.println("--- Ordini ---");
                    System.out.println(body.get("ORDINI"));
                    System.out.println("TOTALE: " + body.get("INCASSI"));
                }else{
                    System.out.println(body.get("message"));
                }
                break;
            }     
        } catch (Exception e) {
            //TODO: handle exception
        }
    }

    public static void gestioneFornitori(){
        try {
            System.out.println("--- FORNITORI ---");
            JSONObject body = postRequest(indirizzo + "/fornitori/getAll.php", "");
            if((Boolean) body.get("success")){
                System.out.println(body.get("data"));
            }else{
                System.out.println(body.get("message"));
            }
            System.out.println("Selezionare una delle seguenti operazioni:");
            System.out.println("1. Aggiungi fornitore\n2. Modifica Email\n3. Manda Email");
            String s = tastiera.readLine();
            switch(s){
                case "1":
                aggiungiFornitore();
                break;
                case "2":
                modificaEmail();
                break;
                case "3":
                mandaEmail();
                break;
            }
        } catch (Exception e) {
            //TODO: handle exception
        }
        
    }

    public static void aggiungiFornitore(){
        JSONObject body = new JSONObject();
        try {
            System.out.println("Nome fornitore:");
            String s = tastiera.readLine();
            body.put("nome", s);
            System.out.println("Email fornitore:");
            s = tastiera.readLine();
            body.put("email", s);
            System.out.println("Indirizzo fornitore:");
            s = tastiera.readLine();
            body.put("indirizzo", s);

            body = postRequest(indirizzo + "/fornitori/aggiungi.php", body.toJSONString());

            System.out.println(body.get("message"));
        } catch (Exception e) {
            //TODO: handle exception
        }
    }

    public static void modificaEmail(){
        JSONObject body = new JSONObject();
        try {
            System.out.println("Nuova Email:");
            String s = tastiera.readLine();
            body.put("email", s);
            System.out.println("Nome Fornitore:");
            s = tastiera.readLine();
            body.put("nome_fornitore", s);

            body = postRequest(indirizzo + "/fornitori/modificaemail.php", body.toJSONString());
            System.out.println(body.get("message"));
        } catch (Exception e) {
            //TODO: handle exception
        }
    }

    public static void mandaEmail(){
        JSONObject body = new JSONObject();
        try {
            System.out.println("Email:");
            String s = tastiera.readLine();
            body.put("email", s);
            System.out.println("Oggetto Email:");
            s = tastiera.readLine();
            body.put("oggetto", s);
            System.out.println("Messaggio Email:");
            s = tastiera.readLine();
            body.put("messaggio", s);

            body = postRequest(indirizzo + "/fornitori/sendemail.php", body.toJSONString());
            System.out.println(body.get("message"));
        } catch (Exception e) {
            //TODO: handle exception
        }
    }

    public static void inserisciProdotto(){
        try {
            System.out.println("Selezionare un operazione:");
            System.out.println("1. Inserisci Prodotto Nuovo\n2. Aggiungi Quantita\n3. Rimuovi Quantita\n4. Modifica Prezzo");
            String s = tastiera.readLine();
            switch(s){
                case "1":
                inserisciNuovoProdotto();
                break;
                case "2":
                aggiungiQuantita();
                break;
                case "3":
                rimuoviQuantita();
                break;
                case "4":
                modificaPrezzo();
                break;
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
        
    }

    public static void inserisciNuovoProdotto(){
        JSONObject body = new JSONObject();
        try {
            System.out.println("Inserisci il nome:");
            String s = tastiera.readLine();
            body.put("nome", s);
            System.out.println("Inserisci la quantita:");
            s = tastiera.readLine();
            body.put("quantita", s);
            System.out.println("Inserisci la categoria:");
            s = tastiera.readLine();
            body.put("categoria", s);
            System.out.println("Inserisci il prezzo:");
            s = tastiera.readLine();
            body.put("prezzo", s);
            System.out.println("Inserisci il nome del fornitore:");
            s = tastiera.readLine();
            body.put("nome_fornitore", s);

            body = postRequest(indirizzo + "/prodotti/aggiungi.php", body.toJSONString());
            System.out.println(body.get("message"));
        } catch (Exception e) {
            //TODO: handle exception
        }
    }

    public static void aggiungiQuantita(){
        JSONObject body = new JSONObject();
        try {
            System.out.println("Inserisci il codice:");
            String s = tastiera.readLine();
            body.put("codice", s);
            System.out.println("Inserisci la quantita:");
            s = tastiera.readLine();
            body.put("quantita", s);

            body = postRequest(indirizzo + "/prodotti/aggiungi.php", body.toJSONString());
            System.out.println(body.get("message"));
        } catch (Exception e) {
            //TODO: handle exception
        }
    }

    public static void rimuoviQuantita(){
        JSONObject body = new JSONObject();
        try {
            System.out.println("Inserisci il codice:");
            String s = tastiera.readLine();
            body.put("codice", s);
            System.out.println("Inserisci la quantita:");
            s = tastiera.readLine();
            body.put("quantita", s);

            body = postRequest(indirizzo + "/prodotti/rimuovi.php", body.toJSONString());
            System.out.println(body.get("message"));
        } catch (Exception e) {
            //TODO: handle exception
        }
    }

    public static void modificaPrezzo(){
        JSONObject body = new JSONObject();
        try {
            System.out.println("Inserisci il codice:");
            String s = tastiera.readLine();
            body.put("codice", s);
            System.out.println("Inserisci il nuovo prezzo:");
            s = tastiera.readLine();
            body.put("prezzo", s);

            body = postRequest(indirizzo + "/prodotti/modificaPrezzo.php", body.toJSONString());
            System.out.println(body.get("message"));
        } catch (Exception e) {
            //TODO: handle exception
        }
    }

    public static void inserisciCategoria(){
        JSONObject body = new JSONObject();
        try {
            System.out.println("Inserisci il nome della nuova categoria:");
            String s = tastiera.readLine();
            body.put("nome", s);
            JSONObject response = postRequest(indirizzo + "/categoria/inserisci.php", body.toJSONString());
            System.out.println(response.get("message"));
        } catch (Exception e) {
            e.printStackTrace();
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

    public static void ricercaProdottoNome(){
        JSONObject body = new JSONObject();
        try {
            System.out.println("Inserisci il nome del prodotto da cercare");
            String nome=tastiera.readLine();
            body.put("nome", nome);
            JSONObject response = postRequest(indirizzo + "/ricerca/nome.php", body.toJSONString());

            if((Boolean) response.get("success")){
                body = new JSONObject();
                System.out.println(response.get("data"));
                

            }else{
                System.out.println(response.get("message"));
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
    }    
}