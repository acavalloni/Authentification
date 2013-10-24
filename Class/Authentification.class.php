<?php 
	//Made by Anthony Cavalloni
	class Authentification{

        private $db;

		public function __construct($db){
			session_start();
            $this->setDb($db);
		}


        public function setDb(PDO $db)
        {
            $this->db = $db;
        }


        public function checkEmail($email){
            $requete = $this->db->query("SELECT email,salt FROM membres WHERE email = '$email'");
            $email_exist = $requete->fetch(PDO::FETCH_ASSOC);
            return $email_exist; //va contenir le mail et le grain de sel de l'utilisateur si il existe
        }


        public function checkPassword($email_exist, $password){
            //on transforme le mot de passe classique en notre mot de passe securisé

            //on récupere l'email et le grain de sel récupés lors du check user
            $email = $email_exist['email'];
            $salt = $email_exist['salt'];

            //on on cypte notre mot de passe a l'aide de notre grain de sel
            $password = crypt($password, '$2a$07$'.$salt.'$');


            //On verifi qu'un compte existe bien avec cette combinaison

            $requete = $this->db->query("SELECT password FROM membres WHERE email = '$email' AND password = '$password'");
            $password_exist = $requete->fetch(PDO::FETCH_ASSOC);

            return $password_exist;
        }


        public function logIn(Membre $membre){
            //on injecte les données de notre objet Membre dans la session
            $_SESSION['User'] = array(
                'id' => $membre->getId() ,
                'nom' => $membre->getNom(),
                'prenom' => $membre->getPrenom(),
                'password' => $membre->getPassword(),
                'salt' => $membre->getSalt(),
                'email' => $membre->getEmail(),
                'statut' => $membre->getStatut()

            );

            return true;
        }


		public function islog(){
			
			if(isset($_SESSION['User']) && isset($_SESSION['User']['id']) && isset($_SESSION['User']['password'])){
                $requete = $this->db->query('SELECT id,password FROM membres WHERE id =\''.$_SESSION['User']['id'].'\' AND password =\''.$_SESSION['User']['password'].'\'');
                $resultat = $requete->fetch(PDO::FETCH_ASSOC);

				if($resultat == true){
					return true;
				}
				else{
					return false;
				}
			}
			else{
				return false;
			}
		}


		public function isAdmin(){
		
			if(isset($_SESSION['User']) && isset($_SESSION['User']['id']) && isset($_SESSION['User']['password'])){
                $requete = $this->db->query('SELECT id,password,statut FROM membres WHERE id =\''.$_SESSION['User']['id'].'\' AND password =\''.$_SESSION['User']['password'].'\'');
                $resultat = $requete->fetch(PDO::FETCH_ASSOC);
				if($resultat == true){
					if($resultat['statut'] == 'admin' && $_SESSION['User']['statut'] == 'admin'){
						return true;
					}
					else{
						return false;
					}
				}
				else{
					return false;
				}
			
			}
            else{
                return false;
            }
		}
	}
