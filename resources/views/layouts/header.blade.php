@include('layouts.navbar')
      <!-- Main Sidebar Container -->
      <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="/" class="brand-link">
            <img id="limanLogo" src="/images/limanlogo.png" height="30" style="opacity: .8;cursor:pointer;" title="Versiyon {{getVersion() . ' Build : ' . getVersionCode()}}">
        </a>
        <!-- Sidebar -->
        <div class="sidebar">  
          <!-- Sidebar Menu -->
          <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
              <!-- Add icons to the links using the .nav-icon class
                   with font-awesome or any other icon font library -->
                @if(count($USER_FAVORITES))
                <li class="nav-header">{{__("Favori Sunucular")}}</li>
                @endif
                @foreach ($USER_FAVORITES as $server)
                    <li class="nav-item has-treeview @if(request('server_id') == $server->id) menu-open @endif">
                    <a href="#" class="nav-link @if(request('server_id') == $server->id) active @endif">
                        <i class="fa fa-server nav-icon"></i>
                        <p>
                            {{$server->name}}
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" @if(request('server_id') == $server->id) style="display: block;" @endif>
                        <li class="nav-item">
                            <a href="/sunucular/{{$server->id}}" class="nav-link">
                                <i class="fa fa-info nav-icon"></i>
                                <p>{{__("Sunucu Detayları")}}</p>
                            </a>
                        </li>
                        @foreach ($server->extensions() as $extension)
                        <li class="nav-item">
                            <a onclick="window.location.href = '/l/{{$extension->id}}/{{$server->city}}/{{$server->id}}'" class="nav-link @if(request('extension_id') == $extension->id) active @endif">
                                <i class="nav-icon {{ empty($extension->icon) ? 'fab fa-etsy' : 'fas fa-'.$extension->icon}}"></i>
                                <p>{{__($extension->display_name)}}</p>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                @endforeach
              <li class="nav-header">{{__("Sunucular")}}</li>
              <li class="nav-item">
                <a href='/sunucular' class="nav-link">
                    <i class="nav-icon fas fa-server"></i>
                    <p>{{__("Sunucular")}}</p>
                </a>
              </li>
              @if(count(extensions()))
                <li class="nav-header">{{__("Eklentiler")}}</li>
                @foreach(extensions() as $extension)
                    <li class="nav-item ext_nav" @if($loop->iteration > getExtensionViewCount())style="display:none;"@endif>
                        @if(request('extension_id') == $extension->id)
                            <a href='/l/{{$extension->id}}' class="nav-link extension-link active">
                        @else
                            <a href='/l/{{$extension->id}}' class="nav-link extension-link">
                        @endif

                        @if(empty($extension->icon))
                            <i class="nav-icon fab fa-etsy"></i>
                        @elseif(substr($extension->icon,0,2) == 'fa')  
                            <i class="nav-icon {{$extension->icon}}"></i>
                        @else
                            <i class="nav-icon fab {{'fas fa-'.$extension->icon}}"></i>
                        @endif
                            <p>{{__($extension->display_name)}}</p>
                        </a>
                    </li>
                @endforeach
                @if(count(extensions()) > getExtensionViewCount())
                <li class="nav-item ext_nav_more_less">
                    <a href="javascript:void(0)" class="nav-link">
                        <p>{{__('...daha fazla')}}</p>
                    </a>
                </li>
                @endif
              @endif
              @if(count(sidebarModuleLinks()))
                <li class="nav-header">{{__("Modül Sayfaları")}}</li>
                    @foreach(sidebarModuleLinks() as $module)
                        <li class="nav-item">
                            <a href='{{$module["url"]}}' class="nav-link">
                                <i class="nav-icon {{$module['icon']}}"></i>
                                <p>{{$module["name"]}}</p>
                            </a>
                        </li>
                    @endforeach
                </li>
              @endif()
              @if(auth()->user()->isAdmin())
                <li class="nav-header">{{__("Yönetim Paneli")}}</li>
                @if(\App\Models\Module::exists())
                <li class="nav-item">
                    <a href='/modules' class="nav-link">
                        <i class="nav-icon fas fa-puzzle-piece"></i>
                        <p>{{__("Modüller")}}</p>
                    </a>
                </li>
                @endif
                <li class="nav-item">
                    <a href='/ayarlar' class="nav-link">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>{{__("Sistem Ayarları")}}</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href='/talepler' class="nav-link">
                        <i class="nav-icon fas fa-plus"></i>
                        <p>{{__("Yetki Talepleri")}}</p>
                        @if(\App\Models\LimanRequest::where('status',0)->count())
                            <span class="badge badge-info right">{{\App\Models\LimanRequest::where('status',0)->count()}}</span>
                        @endif
                    </a>
                </li>
              @else
                <li class="nav-header">{{__("Yetki Talebi")}}</li>
                <li class="nav-item">
                    <a href='/taleplerim' class="nav-link">
                        <i class="nav-icon fas fa-key"></i>
                        <p>{{__("Taleplerim")}}</p>
                    </a>
                </li>
              @endif
              <li class="nav-header">{{__("Hesap")}}</li>
              <li class="nav-item">
                  <a href='/profil' class="nav-link">
                      <i class="nav-icon fas fa-user"></i>
                      <p>{{__("Profil")}}</p>
                  </a>
              </li>
              <li class="nav-item">
                  <a href='/profil/anahtarlarim' class="nav-link">
                      <i class="nav-icon fas fa-user-secret"></i>
                      <p>{{__("Erişim Anahtarları")}}</p>
                  </a>
              </li>
              <li class="nav-item">
                  <a href='/kasa' class="nav-link">
                      <i class="nav-icon fas fa-wallet"></i>
                      <p>{{__("Kasa")}}</p>
                  </a>
              </li>
              <li class="nav-item">
                  <a href='/bilesenler' class="nav-link">
                      <i class="nav-icon fas fa-chart-pie"></i>
                      <p>{{__("Bileşenler")}}</p>
                  </a>
              </li>
              <li class="nav-item">
                  <a href='/takip' class="nav-link">
                      <i class="nav-icon fas fa-grip-horizontal"></i>
                      <p>{{__("Sunucu Takibi")}}</p>
                  </a>
              </li>
            </ul>
          </nav>
          <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
      </aside>