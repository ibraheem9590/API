<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use http\Env\Response;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;



class UserController extends Controller
{

    /**
     *nouveau utilisateur
     * @Route("/api/user-nouveau", name="api_new_user", methods={"GET"})
     */
    public function api_new_userAction(Request $request)
    {
        $counter=0;
        $nom=$request->get('nom');
        if($nom<>null) $counter++;
        $prenom=$request->get('prenom');
        if($prenom<>null) $counter++;

        if ($counter==2){
            $etat=true;
            $em = $this->getDoctrine()->getManager();
            if ($etat==true){
                if ($nom==null){
                    $verification=false;
                    $logger = $this->get('logger');
                    $logger->info('Echec ajout d\'un utilisateur nom invalide');
                    $data[]=[
                        'statut'=>0,
                        'message'=>"Erreur renseigné votre nom",
                    ];
                }
            }

            if ($etat==true){
                if ($prenom==null){
                    $etat=false;
                    $logger = $this->get('logger');
                    $logger->info('Echec ajout d\'un utilisateur prenom invalide');
                    $data[]=[
                        'statut'=>0,
                        'message'=>"Erreur renseigné votre prénom",
                    ];
                }
            }
            if ($etat==true){
                $nomExiste =$em->getRepository(User::class)->findOneBy([
                    'lastname'=>$nom
                ]);
                $prenomExiste =$em->getRepository(User::class)->findOneBy([
                    'firstname'=>$prenom
                ]);
                if ($nomExiste!=null and $prenomExiste!=null){
                    $idNom=$nomExiste->getId();
                    $idPrenom=$prenomExiste->getId();
                    if ($idNom==$idPrenom){
                        $etat=false;
                        $logger = $this->get('logger');
                        $logger->info('Echec ajout d\'un utilisateur utilisateur existe deja');
                        $data[]=[
                            'statut'=>0,
                            'message'=>"L'utilisateur existe déjat",
                        ];
                    }
                }

            }
            if ($etat===true){

                $dateCreation=new \DateTime('now');
                $user=new User();
                $user->setCreationdate($dateCreation);
                $user->setFirstname($prenom);
                $user->setLastname($nom);
                $user->setUpdatedate($dateCreation);
                $em->persist($user);
                $em->flush();
                $logger = $this->get('logger');
                $logger->info('Ajout utilisateur éffectué');

//                $data[]=[
//                    'statut'=>1,
//                    'message'=>"Enregistrement effectuer avec succes",
//                ];
                return $this->redirectToRoute('api_liste_user');
            }
        }else{
            $data[]=[
                'statut'=>0,
                'message'=>"Erreur formulaire ",
                //'form'=>$form
            ];
        }
        /*$serializer = $this->get('jms_serializer');
        $data=$serializer->serialize($CreditExiste, 'json');*/

        return new JsonResponse($data,200);
    }


        /**
     * modification utilisateur
     * @Route("/api/user-modification", name="api_update_user", methods={"GET"})
     */
    public function api_update_userAction(Request $request)
    {
        $counter=0;
        $nom=$request->get('nom');
        if($nom<>null) $counter++;
        $prenom=$request->get('prenom');
        if($prenom<>null) $counter++;
        $id=$request->get('id');
        if($id<>null) $counter++;

        if ($counter==3){
            $etat=true;
            $em = $this->getDoctrine()->getManager();
            if ($etat==true){
                $userExiste =$em->getRepository(User::class)->find($id);
                if ($userExiste==null){
                    $etat=false;
                    $logger = $this->get('logger');
                    $logger->info('Echec de modification d\'un utilisateur');
                    $data[]=[
                        'statut'=>0,
                        'message'=>"Erreur modification ",
                    ];
                }
            }
            if ($etat==true){
                if ($nom==null){
                    $etat=false;
                    $logger = $this->get('logger');
                    $logger->info('Erreur de saisir du nom lors de la modification d\'utilisateur');
                    $data[]=[
                        'statut'=>0,
                        'message'=>"Erreur renseigné votre nom",
                    ];
                }
            }

            if ($etat==true){
                if ($prenom==null){
                    $etat=false;
                    $logger = $this->get('logger');
                    $logger->info('Erreur de saisir du prénom lors de la modification d\'utilisateur');
                    $data[]=[
                        'statut'=>0,
                        'message'=>"Erreur renseigné votre prénom",
                    ];
                }
            }
            if ($etat==true){
                $nomExiste =$em->getRepository(User::class)->findOneBy([
                    'lastname'=>$nom
                ]);
                $prenomExiste =$em->getRepository(User::class)->findOneBy([
                    'firstname'=>$prenom
                ]);
                if ($nomExiste!=null and $prenomExiste!=null){
                    $idNom=$nomExiste->getId();
                    $idPrenom=$prenomExiste->getId();
                    if ($idNom==$idPrenom and $userExiste->getId()!=$idPrenom){
                        $etat=false;
                        $logger = $this->get('logger');
                        $logger->info('Erreur de modification utilisateur car l\'utilisateur existe déja');
                        $data[]=[
                            'statut'=>0,
                            'message'=>"L'utilisateur existe déja",
                        ];
                    }
                }

            }
            if ($etat===true){

                $Updatedate=new \DateTime('now');
                $user=$userExiste;
                $user->setUpdatedate($Updatedate);
                $user->setFirstname($prenom);
                $user->setLastname($nom);
                $em->flush($user);
                $logger = $this->get('logger');
                $logger->info('Modification utilisateur éffectué ');
                $logger->info('Liste utilisateur envoyé');
                return $this->redirectToRoute('api_liste_user');

            }
        }else{
            $data[]=[
                'statut'=>0,
                'message'=>"Erreur formulaire ",
                //'form'=>$form
            ];
        }
        return new JsonResponse($data,200);
    }

    /**
     * liste utilisateur
     * @Route("/api/user-liste", name="api_liste_user", methods={"GET"})
     */
    public function api_liste_userAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $listeUtilisateur =$em->getRepository(User::class)->findAll();
        //dump($resultatat);die();
        if ($listeUtilisateur!=null){
            foreach ($listeUtilisateur as $value){

                    $data[]=[
                        "Id"=>$value->getId(),
                        "Prenom"=>$value->getFirstname(),
                        "Nom"=>$value->getLastname(),
                        "DateCreation"=>date_format($value->getCreationdate(),'Y-m-d H:i:s'),
                        "DateModification"=>date_format($value->getUpdatedate(),'Y-m-d H:i:s'),
                    ];


            }
            $logger = $this->get('logger');
            $logger->info('Envoi de liste utilisateur reussir');
        }else{
            $logger = $this->get('logger');
            $logger->info('Liste utilisateur vide');
            $data[]=[
                'statut'=>0,
                'message'=>"Aucun utilisateur enregistré",
            ];
        }

        return new JsonResponse($data,200);

    }

    /**
     * nouvelle suppression
     * @Route("/api/user-suppression", name="api_delete_user", methods={"GET"})
     */
    public function api_delete_userAction(Request $request)
    {
        $counter=0;
        $id=$request->get('id');
        if($id<>null) $counter++;

        if ($counter==1) {
            $etat = true;
            $em = $this->getDoctrine()->getManager();
            if ($etat == true) {
                $userExiste = $em->getRepository(User::class)->find($id);
                if ($userExiste == null) {
                    $etat = false;
                    $logger = $this->get('logger');
                    $logger->info('Erreur de suppresion utilisateur');
                    $data[] = [
                        'statut' => 0,
                        'message' => "Erreur de suppression ",
                    ];
                }
            }
            if ($etat===true){
                $em->remove($userExiste);
                $em->flush();
                $logger = $this->get('logger');
                $logger->info('suppression utilisateur éffectué');
                return $this->redirectToRoute('api_liste_user');
            }
        }else{
            $logger = $this->get('logger');
            $logger->info('Erreur utilisateur éffectué');
            $data[]=[
                'statut'=>0,
                'message'=>"Erreur formulaire ",
            ];
        }
        return new JsonResponse($data,200);
    }





}
