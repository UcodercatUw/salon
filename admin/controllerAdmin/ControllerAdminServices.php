<?php
require_once __DIR__ . '/../modelAdmin/ModelAdminServices.php';
require_once __DIR__ . '/../../model/ModelServices.php'; // Для получения списка категорий

class ControllerAdminServices {

    // Отображение списка всех услуг
    public function index() {
        $model = new ModelAdminServices();
        $services = $model->getAllServices();
        include __DIR__ . '/../viewAdmin/adminServicesList.php';
    }

    // Добавление новой услуги
    public function add() {
        $serviceModel = new ModelServices();
        $categories = $serviceModel->getCategories();

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $category_id = $_POST['category_id'];
            $title = trim($_POST['title']);
            $description = trim($_POST['description']);
            $price = $_POST['price'];
            $duration = $_POST['duration'];

            // Загрузка изображения по умолчанию
            $imageName = 'default.jpg';

            // Обработка загружаемого файла изображения
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['image']['tmp_name'];
                $fileName = $_FILES['image']['name'];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
                if (in_array($fileExtension, $allowedExtensions)) {
                    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                    $uploadFileDir = __DIR__ . '/../../public/images/';

                    if (!is_dir($uploadFileDir)) {
                        mkdir($uploadFileDir, 0755, true);
                    }

                    if (move_uploaded_file($fileTmpPath, $uploadFileDir . $newFileName)) {
                        $imageName = $newFileName;
                    }
                } else {
                    $error = 'Недопустимый формат файла. Разрешены: jpg, jpeg, png, webp.';
                }
            }

            if (empty($error)) {
                $model = new ModelAdminServices();
                if ($model->addService($category_id, $title, $description, $price, $duration, $imageName)) {
                    header('Location: index.php');
                    exit;
                } else {
                    $error = 'Ошибка при добавлении услуги в базу данных.';
                }
            }
        }

        include __DIR__ . '/../viewAdmin/adminServiceAdd.php';
    }

    // Редактирование существующей услуги
    public function edit($id) {
        $model = new ModelAdminServices();
        $service = $model->getServiceById($id);

        if (!$service) {
            header('Location: index.php');
            exit;
        }

        $serviceModel = new ModelServices();
        $categories = $serviceModel->getCategories();

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $category_id = $_POST['category_id'];
            $title = trim($_POST['title']);
            $description = trim($_POST['description']);
            $price = $_POST['price'];
            $duration = $_POST['duration'];
            
            // Старое имя изображения остается, если новое не было выбрано
            $imageName = $service['image'];

            // Если выбрали новое изображение
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['image']['tmp_name'];
                $fileName = $_FILES['image']['name'];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
                if (in_array($fileExtension, $allowedExtensions)) {
                    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                    $uploadFileDir = __DIR__ . '/../../public/images/';

                    if (!is_dir($uploadFileDir)) {
                        mkdir($uploadFileDir, 0755, true);
                    }

                    if (move_uploaded_file($fileTmpPath, $uploadFileDir . $newFileName)) {
                        $imageName = $newFileName;
                    }
                } else {
                    $error = 'Недопустимый формат файла. Разрешены: jpg, jpeg, png, webp.';
                }
            }

            if (empty($error)) {
                if ($model->updateService($id, $category_id, $title, $description, $price, $duration, $imageName)) {
                    header('Location: index.php');
                    exit;
                } else {
                    $error = 'Ошибка при обновлении услуги.';
                }
            }
        }

        include __DIR__ . '/../viewAdmin/adminServiceEdit.php';
    }

    // Удаление услуги
    public function delete($id) {
        if ($id > 0) {
            $model = new ModelAdminServices();
            $model->deleteService($id);
        }
        header('Location: index.php');
        exit;
    }
}
?>