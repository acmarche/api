<?php

namespace AcMarche\Api\Controller;

use AcMarche\Api\Entity\User;
use AcMarche\Api\Form\UserEditType;
use AcMarche\Api\Form\UserType;
use AcMarche\Api\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/user')]
#[IsGranted('ROLE_API_ADMIN')]
class UserController extends AbstractController
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordEncoder,
        private UserRepository $userRepository,
    ) {
    }

    #[Route(path: '/', name: 'api_user_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render(
            '@AcMarcheApi/user/index.html.twig',
            [
                'users' => $this->userRepository->findAll(),
            ]
        );
    }

    #[Route(path: '/new', name: 'api_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $user->setPassword(
                $this->userPasswordEncoder->hashPassword($user, $user->getPassword())
            );
            $this->userRepository->persist($user);
            $this->userRepository->flush();
            $this->addFlash('success', 'Le user a bien été ajouté');

            return $this->redirectToRoute('api_user_index');
        }

        return $this->render(
            '@AcMarcheApi/user/new.html.twig',
            [
                'user' => $user,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'api_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render(
            '@AcMarcheApi/user/show.html.twig',
            [
                'user' => $user,
            ]
        );
    }

    #[Route(path: '/{id}/edit', name: 'api_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->userRepository->flush();
            $this->addFlash('success', 'Le user a bien été modifié');

            return $this->redirectToRoute('api_user_index');
        }

        return $this->render(
            '@AcMarcheApi/user/edit.html.twig',
            [
                'user' => $user,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'api_user_delete', methods: ['DELETE'])]
    public function delete(Request $request, User $user): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $this->userRepository->remove($user);
            $this->userRepository->flush();
            $this->addFlash('success', 'Le user a bien été supprimé');
        }

        return $this->redirectToRoute('api_user_index');
    }
}
