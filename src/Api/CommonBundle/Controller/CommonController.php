<?php

namespace Api\CommonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
  Extend for all our controllers, a few overrides and general methods
 */
class CommonController extends Controller
{

    public $format = 'xml';
    public $em;

    /**
     * {@inheritdoc}
     *
     * In addition this function calls initialize.
     *
     * @see CommonController::initialize
     */
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize();
    }

    /**
     * Initialize all controllers providing them with an easily accessible
     * entity manager and request format.
     */
    public function initialize()
    {
        $this->format = $this->getRequest()->getRequestFormat();

        // Check if a _format header is set in the request
        if ('' != trim($this->getRequest()->headers->get('_format'))) {
            $this->format = trim($this->getRequest()->headers->get('_format'));
        }

        $this->em = $this->getDoctrine()->getEntityManager();
    }

    /**
     * To easily retrieve an entity manager for the extending controllers
     *
     * @param string $connection
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function em($connection = 'default')
    {
        return $this->getDoctrine()->getEntityManager($connection);
    }

    /**
     * Call external URL. Default method is GET, unless POST data is filled
     *
     * @param string $url      url
     * @param string $postData post data
     *
     * @return array
     */
    public function callUrl($url, $postData = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);

        if (!empty($postData)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            curl_setopt($ch, CURLOPT_POST, 1);
        }

        $response = curl_exec($ch);

        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);

        $result = array();
        $result['header'] = substr($response, 0, $headerSize);
        $result['content'] = substr($response, $headerSize);
        $result['error'] = curl_error($ch);
        $result['code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        return $result;
    }

    /**
     * map data
     * @param string $data
     * @param string $bundle
     * @param string $class
     * @param integer $objectId
     * 
     * @return type
     * 
     * @throws \BadMethodCallException
     */
    protected function mapData($data, $bundle, $class, $objectId=null)    
    {        
        if ($this->format == "json") {    
            $mapperName = "Api\\".$bundle."Bundle\\Mapper\\".$class."Mapper";
          
            $mapper = new $mapperName;           
            $mapper->setDoctrine($this->getDoctrine());            
            $mapper->setDeserializer($this->get("serializer"));            
          
            return $mapper->map($data, $objectId);        
            
        } else {            
            throw new \BadMethodCallException($this->format . " is currently not supported for this request");   
        }    
    
    }
    
    /**
     * process object
     * 
     * @param type $object
     * 
     * @throws HttpException
     */
    protected function processData($object)
    {
        if ($object->getId()) {
            $object = $this->em->merge($object);
        } else {
            $this->em->persist($object);
        }
        
        $errors = $this->get('validator')->validate($object);
        
        if (count($errors) == 0) {            
            try {                
                $this->em->flush();            
            } catch (\PDOException $e) {                
                throw new HttpException(400, "Unable to store object (error code returned: ".$e->getCode().")");
            }       
        } else {            
            $iterator = $errors->getIterator();            
            $msg = "Bad data\n";            
            foreach ($iterator as $violation) {                
                $msg .= $violation->getMessageTemplate() . 
                        ' (field: ' . $violation->getPropertyPath() . 
                        ', value: '. $violation->getInvalidValue() . ")\n";            
            }            
            throw new HttpException(400, $msg);                   
        }    
    }
}
