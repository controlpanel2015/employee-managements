<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Form\EmployeeType;
use App\Repository\EmployeeRepository;
use App\Service\AgeCalculator;
use Doctrine\ORM\EntityManager;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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
     * @Route("/list/{page}", name="employee_index", methods={"GET"}, defaults={"page": 1} , requirements={"page"="\d+"})
     * @Security("is_granted('ROLE_ADMIN')")
     * @throws \Exception
     */
    public function index(EmployeeRepository $employeeRepository, Request $request,PaginatorInterface $paginator, $page = 1): Response
    {
        $searchCriteria = $request->query->all();
        $employeeQuery = $employeeRepository->getQueryBySearchCriteria($searchCriteria);
        // Paginate the results of the query
        $employees = $paginator->paginate(
        // Doctrine Query, not results
            $employeeQuery,
            // Define the page parameter
            $page,
            // Items per page
            2
        );
        return $this->render(
            'employee/index.html.twig',
            [
                'employees' => $employees,
                'genders' => ['M' => "Male", 'F' => "Female", 'O' => "Others"],
                'search' => $searchCriteria,
            ]
        );

    }


    /**
     *
     * @IsGranted("ROLE_ADMIN")
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
            $this->addFlash('success', "Data has been added successfully");

            return $this->redirectToRoute('employee_index');
        }

        return $this->render(
            'employee/new.html.twig',
            [
                'employee' => $employee,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     *
     * @Route("/report", name="employee_report", methods={"GET"})
     * @Security("is_granted('ROLE_ADMIN')")
     * @throws \Exception
     */
    public function showReport(EmployeeRepository $employeeRepository): Response
    {
        //$employeeRepository =$entityManager->getRepository('Employee');
        $employees = $employeeRepository->getGenderWiseEmployee();

        return $this->render(
            'employee/report.html.twig',
            [
                'employee_list' => $employees,
            ]
        );
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/{id}", name="employee_show", methods={"GET"})
     */
    public function show(Employee $employee): Response
    {
        return $this->render(
            'employee/show.html.twig',
            [
                'employee' => $employee,
            ]
        );
    }

    /**
     * @Route("/{id}/edit", name="employee_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Employee $employee): Response
    {
        $form = $this->createForm(EmployeeType::class, $employee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute(
                'employee_index',
                [
                    'id' => $employee->getId(),
                ]
            );
        }

        return $this->render(
            'employee/edit.html.twig',
            [
                'employee' => $employee,
                'form' => $form->createView(),
            ]
        );
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


}
