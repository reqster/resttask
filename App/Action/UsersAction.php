<?php
namespace App\Action;

use Doctrine\ORM\EntityManager;
use Respect\Validation\Validator as v;

class UsersAction
{
    private $em;
    private $fields;

    private function fillFields($request){
        $paramlist=$request->getParsedBody();
        $fieldList = array();
        foreach ($this->fields as $key => $value) {
            if (isset($paramlist[$value]) && !empty($paramlist[$value]))
                $fieldList[$key] = $paramlist[$value];
        }
        return $fieldList;
    }

    private function error($msg){
        return array('Error' => $msg);
    }

    private function errorIdMissing(){
        return $this->error('Missing field: id');
    }

    private function errorNoFields(){
        return $this->error('No fields specified, check parameters spelling');
    }

    private function errorUserNotFound(){
        return $this->error('User not found');
    }

    private function success(){
        return 'Success';
    }

    private function validateFields($fields){
        $r = array();
        if (isset($fields['email']) && !(v::email()->validate($fields['email'])))
            $r['Invalid'][] = 'email';
        if (isset($fields['phone']) && !(v::phone()->validate($fields['phone'])))
            $r['Invalid'][] = 'phone';        
        return $r;
    }

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->fields = array(
            "lastName"      => "lastname",
            "firstName"     => "firstname",
            "patronymic"    => "patronymic",
            "phone"         => "phone",
            "email"         => "email"
            );
    }

    public function delete($request, $response, $args){
        // Obviously all actions are supposed to require some sort of authorization, implementation of which is skipped for this test task
        $response = $response->withStatus(200); // Woudln't always be 200 for production
        $paramlist=$request->getParsedBody();
        if (isset($paramlist['id'])){
            if ($this->em->getRepository('App\Entity\Usr')->deleteById($paramlist['id']))
                return $response->withJSON($this->success());
            else{
                return $response->withJSON($this->errorUserNotFound());   
            }
        }
        else
            return $response->withJSON($this->errorIdMissing());            
    }

    public function get($request, $response, $args){
        $response = $response->withStatus(200); // Woudln't always be 200 for production
        $findParams = array();
        // Search by Id is intentionally not supported
        // Pagination should be implemented for a real world service
        foreach ($this->fields as $key => $value) {
                if (null !== $request->getQueryParam($value))
                    $findParams[$key] = $request->getQueryParam($value);
        }
        if (!empty($findParams)){
            $userList = $this->em->getRepository('App\Entity\Usr')->findFiltered($findParams);
            if ($userList) {
                // Here we should remove fields which shouldn't be visible, like passwords
                return $response->withJSON($userList);
            }
            return $response->withJSON($this->error('No records found'));
        }
        return $response->withJSON($this->errorNoFields());
    }

    public function post($request, $response, $args){
        $response = $response->withStatus(200); // Woudln't always be 200 for production
        $output = array();
        $fieldList = $this->fillFields($request);
        foreach ($this->fields as $key => $value) {
            if (!isset($fieldList[$key]))
                $output['Missing'][] = $value;
        }
        $validated = $this->validateFields($fieldList);
        if (!empty($validated))
            $output['Invalid'] = $validated['Invalid'];
        if (!empty($output)){
            $output['Error'] = 'Incorrect input';
            return $response->withJSON($output);
        }
        try{
        $this->em->getRepository('App\Entity\Usr')->add($fieldList);
        }
        catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
            return $response->withJSON($this->error('E-Mail already reserved'));
        }
        return $response->withJSON($this->success());
    }

    public function put($request, $response, $args){
        $response = $response->withStatus(200); // Woudln't always be 200 for production
        $paramlist=$request->getParsedBody();
        if (isset($paramlist['id'])){
            $fieldList = $this->fillFields($request);
            if (empty($fieldList))
                return $response->withJSON($this->errorNoFields());
            $validated = $this->validateFields($fieldList);
            if (!empty($validated)){
                $validated['Error'] = 'Invalid field value';
                return $response->withJSON($validated);
            }
            try {
            $outcome = $this->em->getRepository('App\Entity\Usr')->updateById($paramlist['id'], $fieldList);
            }
            catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
                return $response->withJSON($this->error('E-Mail already reserved'));
            }
            if (!$outcome)
                return $response->withJSON($this->errorUserNotFound());
            else{
                return $response->withJSON($this->success());}
            }
        else{
            return $response->withJSON($this->errorIdMissing());
        }
    }

}