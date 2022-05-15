import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.net.HttpURLConnection;
import java.net.URL;

import org.json.simple.JSONObject;
import org.json.simple.parser.JSONParser;
import org.json.simple.parser.ParseException;

public class ClientMagazzino {
    public static void main(String[] args) throws java.io.IOException{

        // Client client = new Client();
        // client.login();
        JSONObject body = new JSONObject();
        JSONParser parser = new JSONParser();
        BufferedReader tastiera = new BufferedReader(new InputStreamReader(System.in));
        final String indirizzo = "http://localhost/Supermercato/api";
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
                            System.out.println("1. Per nome\n2. Per genere");
                            scelta = tastiera.readLine();
                            switch(scelta){
                                case "1":
                                try {
                                    System.out.println("Inserisci il nome del prodotto da cercare");
                                    String nome=tastiera.readLine();
                                    body.put("prodotto",nome);
                                    JSONObject response = postRequest(indirizzo + "/ricerca/nome.php", body.toJSONString());

                                    if((Boolean) response.get("success")){
                                        System.out.println(response.get("data"));
                                        // todo fare istruzioni che fa inserire il codice del prodotto e la quantita da mettere nel carrello
                                    }else{
                                        System.out.println(response.get("message"));
                                    }
                                } catch (Exception e) {
                                    e.printStackTrace();
                                }
                                break;
                                case "2":
                                break;
                            }
                            break;
                            case "2":
                            break;
                            case "3":
                            break;
                        }
                    }
                }
                break;
                case "2":
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
                break;
                case "3":
                System.out.println("Inserire l'email:");
                s = tastiera.readLine();
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

                break;
                case "exit":
                break;
            }
        }

    }

    public static JSONObject postRequest(String indirizzo,String body) throws ParseException{
        String risposta = null;
        JSONParser parser = new JSONParser();
        try{
            URL uri = new URL(indirizzo);
            HttpURLConnection con = (HttpURLConnection) uri.openConnection();
            con.setRequestMethod("POST");
            con.setDoInput(true);
            con.setDoOutput(true);
            OutputStream os = con.getOutputStream();
            os.write(body.getBytes());
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
        
        String i = "http://localhost/Supermercato/api/accesso/login.php";
        try {
            JSONObject response = postRequest(i, body.toJSONString());
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
}

