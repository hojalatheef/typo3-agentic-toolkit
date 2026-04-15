<?php

declare(strict_types=1);

namespace MyVendor\MyExtension\Module\AccessGate;

use TYPO3\CMS\Backend\Module\ModuleAccessGateInterface;
use TYPO3\CMS\Backend\Module\ModuleAccessResult;
use TYPO3\CMS\Backend\Module\ModuleInterface;
use TYPO3\CMS\Core\Attribute\AsModuleAccessGate;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;

/**
 * Module access gate (Feature-109365, TYPO3 ^14.2).
 * Use with 'access' => 'myEditor' in Configuration/Backend/Modules.php.
 */
#[AsModuleAccessGate(identifier: 'myEditor', after: ['user'])]
final readonly class EditorGate implements ModuleAccessGateInterface
{
    /** UID of the be_groups record whose members may use the module. */
    private const EDITOR_GROUP_UID = 3;

    public function decide(
        ModuleInterface $module,
        BackendUserAuthentication $user,
    ): ModuleAccessResult {
        if ($module->getAccess() !== 'myEditor') {
            return ModuleAccessResult::Abstain;
        }
        if ($user->isAdmin()) {
            return ModuleAccessResult::Granted;
        }
        return in_array(self::EDITOR_GROUP_UID, $user->userGroupsUID, true)
            ? ModuleAccessResult::Granted
            : ModuleAccessResult::Denied;
    }
}
