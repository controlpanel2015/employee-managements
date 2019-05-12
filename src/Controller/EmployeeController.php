<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Form\EmployeeType;
use App\Repository\EmployeeRepository;
use App\Service\AgeCalculator;
use App\Service\FileUploader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/employee")
 */
class EmployeeController extends AbstractController
{
    /**
     *
     * @Route("/", name="employee_index", methods={"GET"})
     */
    public function index(EmployeeRepository $employeeRepository,AgeCalculator $ageCalculator,Request $request): Response
    {
        $search_name=$request->query->get('name');
        $search_age=$request->query->get('age');
        $date_of_birth=null;
        if($search_age>0){
            $date_of_birth=new \DateTime(date('Y-m-d',strtotime("-".$search_age." year ".date('Y-m-d'))));
        }
        if($search_name!='' &&$date_of_birth!=null){
            $employees=$employeeRepository->findBy(['name'=>$search_name,'date_of_birth'=>$date_of_birth]);
        }else if($search_name!='' &&$date_of_birth==null){
            $employees=$employeeRepository->findBy(['name'=>$search_name]);
        }else if($search_name=='' && $date_of_birth!=null){
            $employees=$employeeRepository->findBy(['date_of_birth'=>$date_of_birth]);
        }else{
            $employees= $employeeRepository->findAll();
        }

        $age_list=[];
        $genders=['M'=>"Male",'F'=>"Female",'O'=>"Others"];
        foreach ($employees as $employee){
            $age_list[$employee->getId()]=$ageCalculator->calculate_age($employee->getDateOfBirth());
        }
        //$gender_wise_employee_list=$employeeRepository->getGenderWiseEmployee();
       //dump($gender_wise_employee_list);
        return $this->render('employee/index.html.twig', [
            'employees'=>$employees,'age_list'=>$age_list,'genders'=>$genders,'name'=>$search_name,'age'=>$search_age
        ]);
    }
    /*public function index(EmployeeRepository $employeeRepository,AgeCalculator $ageCalculator): Response
    {
        $employees= $employeeRepository->findAll();
        $json_array=[];
        $i=0;
        $upload_directory=$this->getParameter('upload_directory');
        foreach ($employees as $employee){
            $age_list[$employee->getId()]=$ageCalculator->calculate_age($employee->getDateOfBirth());
            $json_array[$i]['id']=$employee->getId();
            $json_array[$i]['name']=$employee->getName();
            $json_array[$i]['age']=$ageCalculator->calculate_age($employee->getDateOfBirth());
            $json_array[$i]['gender']=$employee->getGender()=="M"?"Male":"Female";
            $json_array[$i]['note']=$employee->getNote();
            $json_array[$i]['image']="<img src = '".$upload_directory."/".$employee->getImage()."'/>";
            $path="employee/".$employee->getId()."/edit";
            $json_array[$i]['action']="<a href='".$path."'><span><i class='far fa-edit'></i></span></a>";
            $i++;
        }
        return $this->json($json_array);


        return $this->render('employee/index.html.twig', [
            'employees'=>$employees,'age_list'=>$age_list,'genders'=>$genders
        ]);
    }*/

    /**
     *
     * @Route("/new", name="employee_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $employee = new Employee();
        $form = $this->createForm(EmployeeType::class, $employee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($employee);
            $entityManager->flush();
            $this->addFlash('success',"Data has been added sucessfully");
            return $this->redirectToRoute('employee_index');
        }

        return $this->render('employee/new.html.twig', [
            'employee' => $employee,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="employee_show", methods={"GET"})
     */
    public function show(Employee $employee): Response
    {
        return $this->render('employee/show.html.twig', [
            'employee' => $employee,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="employee_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Employee $employee,FileUploader $fileUploader): Response
    {
        $form = $this->createForm(EmployeeType::class, $employee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('employee_index', [
                'id' => $employee->getId(),
            ]);
        }

        return $this->render('employee/edit.html.twig', [
            'employee' => $employee,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="employee_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Employee $employee): Response
    {
        if ($this->isCsrfTokenValid('delete'.$employee->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($employee);
            $entityManager->flush();
        }

        return $this->redirectToRoute('employee_index');
    }

    /**
     * @Route("/report/{employee}", name="report_index", methods={"GET"})
     */
    public function gender_wise_employee_list(EmployeeRepository $employeeRepository): Response
    {
        $employees= $employeeRepository->getGenderWiseEmployee();
        return $this->render('employee/report.html.twig', [
            'employee_list'=>$employees
        ]);
    }
}
