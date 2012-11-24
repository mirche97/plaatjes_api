<?php

namespace Api\CommonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;

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

//    /**
//     * Visual entry point of the backend application
//     *
//     * @return \Symfony\Component\HttpFoundation\Response
//     */
//    public function entryAction()
//    {
//        return $this->render('cruisetravelCommonBundle:Common:entry.' . $this->format . '.twig');
//    }

//    /**
//     * Paginates the specified data
//     * 
//     * TODO: Might be possible that we throw an exception when we
//     * are requesting a page that is below or above our page count etc.
//     *
//     * The paginator can paginate:
//     * - array
//     * - Doctrine\ORM\Query
//     * - Doctrine\ORM\QueryBuilder
//     * - Doctrine\ODM\MongoDB\Query\Query
//     * - Doctrine\ODM\MongoDB\Query\Builder
//     * - Doctrine\Common\Collection\ArrayCollection - any doctrine relation collection including
//     * - ModelCriteria - Propel ORM query
//     * - array with Solarium_Client and Solarium_Query_Select as elements
//     * 
//     * @param  mixed $data
//     * @return mixed
//     */
//    protected function paginator($data)
//    {
//        $curPage = $this->get('request')->query->get('page', 1);
//        
//        if ($this->get('request')->query->get('maxResults') > 0) {
//            $maxPerPage = $this->get('request')->query->get('maxResults');
//        } else {
//            $maxPerPage = $this->container->getParameter("rest.max_resource_per_page");
//        }
//
//        $paginator = $this->get('knp_paginator');
//        $pagination = $paginator->paginate($data, $curPage, $maxPerPage);
//
//        return $pagination;
//    }

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
}
