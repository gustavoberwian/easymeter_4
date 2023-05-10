<!-- start: sidebar -->
<aside id="sidebar-left" class="sidebar-left">
    <div class="sidebar-header">
        <div class="sidebar-toggle d-none d-md-block" data-toggle-class="sidebar-left-collapsed" data-target="html" data-fire-event="sidebar-left-toggle">
            <i class="fas fa-bars"></i>
        </div>
    </div>
    <div class="nano">
        <div class="nano-content">
            <nav id="menu" class="nav-main" role="navigation">
                <ul class="nav nav-main">
					<li class="<?php if(in_array($method, array('index'))) echo 'nav-active'; ?>">
						<a class="nav-link" href="<?= site_url($url); ?>">
							<i class="bx bx-home-alt" aria-hidden="true"></i>
							<span>Início</span>
						</a>                        
					</li>
                    <li class="<?php if(in_array($method, array('alerts'))) echo 'nav-active'; ?>">
					<a class="nav-link" href="<?= site_url($url . '/alerts'); ?>">
						<i class="bx bx-bell" aria-hidden="true"></i>
						<span>Alertas</span>
					</a>
				</li>
                </ul>
            </nav>
        </div>
    </div>
</aside>
<!-- end: sidebar -->