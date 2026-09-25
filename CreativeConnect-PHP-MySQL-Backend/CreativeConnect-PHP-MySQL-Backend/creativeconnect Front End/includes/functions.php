<?php
declare(strict_types=1);

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_input(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void
{
    $submitted = $_POST['csrf_token'] ?? '';
    if (!is_string($submitted) || !hash_equals(csrf_token(), $submitted)) {
        http_response_code(419);
        exit('Your form session expired. Please go back, refresh the page and try again.');
    }
}

function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function take_flash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return is_array($flash) ? $flash : null;
}

function logged_in(): bool
{
    return isset($_SESSION['user']) && is_array($_SESSION['user']);
}

function current_user(): ?array
{
    return logged_in() ? $_SESSION['user'] : null;
}

function is_admin(): bool
{
    return logged_in() && ($_SESSION['user']['role'] ?? '') === 'admin';
}

function require_login(): void
{
    if (!logged_in()) {
        set_flash('error', 'Please log in to continue.');
        redirect('login.php');
    }
}

function require_admin(): void
{
    if (!is_admin()) {
        http_response_code(403);
        exit('You do not have permission to access this page.');
    }
}

function service_options(): array
{
    return [
        'video-editing' => 'Video & Digital Editing',
        'web-development' => 'Web Development',
        'content-creation' => 'Content Creation',
        'graphic-design' => 'Graphic Design',
        'it-support' => 'IT Support & Consulting',
        'other' => 'Other',
    ];
}

function status_options(): array
{
    return [
        'submitted' => 'Submitted',
        'under-review' => 'Under Review',
        'in-progress' => 'In Progress',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ];
}

function validate_project_request(array $input): array
{
    $values = [
        'fullName' => trim((string) ($input['fullName'] ?? '')),
        'email' => strtolower(trim((string) ($input['email'] ?? ''))),
        'company' => trim((string) ($input['company'] ?? '')),
        'service' => trim((string) ($input['service'] ?? '')),
        'deadline' => trim((string) ($input['deadline'] ?? '')),
        'budget' => trim((string) ($input['budget'] ?? '')),
        'message' => trim((string) ($input['message'] ?? '')),
    ];
    $errors = [];

    if (mb_strlen($values['fullName']) < 2 || mb_strlen($values['fullName']) > 100) {
        $errors['fullName'] = 'Enter a name between 2 and 100 characters.';
    }
    if (!filter_var($values['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($values['email']) > 190) {
        $errors['email'] = 'Enter a valid email address.';
    }
    if (mb_strlen($values['company']) < 2 || mb_strlen($values['company']) > 120) {
        $errors['company'] = 'Enter a company or brand name between 2 and 120 characters.';
    }
    if (!array_key_exists($values['service'], service_options())) {
        $errors['service'] = 'Select a valid service.';
    }

    $deadline = DateTimeImmutable::createFromFormat('!Y-m-d', $values['deadline']);
    $dateErrors = DateTimeImmutable::getLastErrors();
    $invalidDate = $deadline === false || ($dateErrors !== false && ($dateErrors['warning_count'] > 0 || $dateErrors['error_count'] > 0));
    if ($invalidDate) {
        $errors['deadline'] = 'Choose a valid deadline.';
    } elseif ($deadline < new DateTimeImmutable('today')) {
        $errors['deadline'] = 'Deadline must be today or later.';
    }

    if (!is_numeric($values['budget']) || (float) $values['budget'] < 100 || (float) $values['budget'] > 1000000) {
        $errors['budget'] = 'Budget must be between 100 and 1,000,000 AUD.';
    }
    if (mb_strlen($values['message']) < 20 || mb_strlen($values['message']) > 3000) {
        $errors['message'] = 'Project brief must be between 20 and 3,000 characters.';
    }

    return [$values, $errors];
}

function find_request(PDO $pdo, int $id): ?array
{
    $statement = $pdo->prepare(
        'SELECT project_requests.*, users.full_name AS account_name, users.email AS account_email
         FROM project_requests
         JOIN users ON users.id = project_requests.user_id
         WHERE project_requests.id = :id'
    );
    $statement->execute(['id' => $id]);
    $request = $statement->fetch();
    return $request ?: null;
}

function can_manage_request(array $request): bool
{
    return is_admin() || (int) $request['user_id'] === (int) (current_user()['id'] ?? 0);
}

function require_request_access(array $request): void
{
    if (!can_manage_request($request)) {
        http_response_code(403);
        exit('You do not have permission to manage this request.');
    }
}

function request_id_from_input(mixed $value): int
{
    $id = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($id === false) {
        http_response_code(400);
        exit('Invalid request ID.');
    }
    return (int) $id;
}

function safe_login_destination(mixed $value): string
{
    $allowed = ['dashboard.php', 'contact.php'];
    return is_string($value) && in_array($value, $allowed, true) ? $value : 'dashboard.php';
}
