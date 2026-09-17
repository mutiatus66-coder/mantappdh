import time
import os
import sys

try:
    import selenium
except ImportError:
    possible_paths = [
        os.path.expanduser('~/.venv/selenium/lib/python3.14/site-packages'),
        os.path.expanduser('~/.venv/selenium/lib/python3.13/site-packages'),
        '/tmp/selenium_env/lib/python3.14/site-packages',
        '/tmp/selenium_env/lib/python3.13/site-packages',
    ]
    for p in possible_paths:
        if os.path.exists(p) and p not in sys.path:
            sys.path.insert(0, p)

from selenium.webdriver.common.by import By
from selenium.webdriver.common.keys import Keys

def sleep(seconds):
    time.sleep(seconds)

def take_screenshot_on_error(driver, error_name):
    try:
        error_dir = os.path.join(os.getcwd(), 'selenium', 'screenshot')
        if not os.path.exists(error_dir):
            os.makedirs(error_dir, exist_ok=True)
        clean_error_name = error_name.split('_')[0] if '_' in error_name else error_name
        filename = os.path.join(error_dir, f"error_{clean_error_name}.png")
        driver.save_screenshot(filename)
        print(f"📸 Screenshot error disimpan di: {filename}")
    except Exception as err:
        print("Gagal mengambil screenshot:", err)

def take_screenshot_session(driver, session_name):
    try:
        screenshot_dir = os.path.join(os.getcwd(), 'selenium', 'screenshot')
        if not os.path.exists(screenshot_dir):
            os.makedirs(screenshot_dir, exist_ok=True)
        filename = os.path.join(screenshot_dir, f"{session_name}.png")
        driver.save_screenshot(filename)
        print(f"📸 Screenshot sesi disimpan di: {filename}")
    except Exception as err:
        print("Gagal mengambil screenshot sesi:", err)

def highlight_element(driver, element, duration=0.8):
    """
    Menampilkan outline aksen biru elegan (simple & profesional) pada elemen yang akan diklik/diisi
    sehingga penonton video dapat melihat dengan jelas lokasi elemen secara rapi.
    """
    try:
        if not element:
            return
        driver.execute_script("""
            let el = arguments[0];
            if (!el) return;
            
            if (!document.getElementById('selenium-simple-highlight-style')) {
                let style = document.createElement('style');
                style.id = 'selenium-simple-highlight-style';
                style.innerHTML = `
                    .selenium-highlight-simple {
                        outline: 3px solid #2563eb !important;
                        outline-offset: 2px !important;
                        background-color: rgba(37, 99, 235, 0.12) !important;
                        border-radius: 4px !important;
                        transition: all 0.2s ease-in-out !important;
                    }
                `;
                document.head.appendChild(style);
            }
            
            el.scrollIntoView({block: 'center', inline: 'center'});
            el.classList.add('selenium-highlight-simple');
        """, element)
        time.sleep(duration)
        driver.execute_script("""
            let el = arguments[0];
            if (el) el.classList.remove('selenium-highlight-simple');
        """, element)
    except Exception:
        pass

def highlight_and_click(driver, element, duration=0.8):
    """
    Menghighlight elemen dengan outline aksen biru bersih, lalu mengklik elemen tersebut.
    """
    if not element:
        return
    highlight_element(driver, element, duration=duration)
    driver.execute_script("arguments[0].click();", element)

def login_admin_with_highlights(driver, base_url, email='admin@demo.test', password='password'):
    from selenium.webdriver.support.ui import WebDriverWait
    from selenium.webdriver.support import expected_conditions as EC

    print("1. Membuka landing page...")
    driver.get(base_url)
    sleep(3)  # Delay agar penonton video panduan dapat melihat tampilan landing page secara jelas

    print("2. Menekan tombol Login pada Landing Page...")
    try:
        btn_login = WebDriverWait(driver, 10).until(
            EC.presence_of_element_located((By.CSS_SELECTOR, '.btn-login, a[href*="/login"], a.nav-link[href*="/login"]'))
        )
        highlight_and_click(driver, btn_login, duration=1.5)
        sleep(2.5)  # Delay perpindahan ke halaman login
    except Exception:
        driver.get(f"{base_url}/login")
        sleep(2.5)

    print("3. Memasukkan kredensial login admin...")
    inp_email = WebDriverWait(driver, 10).until(EC.presence_of_element_located((By.NAME, 'email')))
    highlight_element(driver, inp_email, duration=1.0)
    inp_email.send_keys(email)
    sleep(0.8)

    inp_pass = driver.find_element(By.NAME, 'password')
    highlight_element(driver, inp_pass, duration=1.0)
    inp_pass.send_keys(password)
    sleep(0.8)

    print("   -> Menekan tombol Masuk/Submit...")
    try:
        btn_masuk = driver.find_element(By.CSS_SELECTOR, 'button[type="submit"], input[type="submit"]')
        highlight_and_click(driver, btn_masuk, duration=1.5)
    except Exception:
        pass
    sleep(3)  # Delay memuat dashboard admin

    print("4. Berhasil masuk ke Dashboard Admin...")
    take_screenshot_session(driver, 'Admin_Dashboard_Loaded')
    sleep(1.5)

def demo_toggle_theme(driver, session_prefix='User'):
    print(f"      -> Demonstrasi Toggle Theme ({session_prefix})...")
    try:
        menu_open = driver.execute_script("""
            let menu = document.querySelector('[data-kt-element="theme-mode-menu"]');
            if (menu) {
                let trigger = menu.parentElement.querySelector('[data-kt-menu-trigger]') || menu.previousElementSibling;
                if (trigger && window.KTMenu) {
                    let item = window.KTMenu.getInstance(trigger) || window.KTMenu.getInstance(menu);
                    if (item) {
                        item.show(trigger);
                        return true;
                    }
                }
                menu.classList.add('show');
                menu.style.display = 'block';
                return true;
            }
            return false;
        """)
        if menu_open:
            sleep(2)  # Delay agar dropdown pilihan (Light, Dark, System) terlihat jelas saat direkam video
            take_screenshot_session(driver, f"{session_prefix}_Theme_Menu_Open")

            # Klik pilihan Dark
            driver.execute_script("""
                let darkBtn = document.querySelector('[data-kt-element="mode"][data-kt-value="dark"]');
                if(darkBtn) darkBtn.click();
            """)
            sleep(2)  # Delay animasi pergantian tema gelap

            # Buka kembali dropdown di Tema Gelap
            driver.execute_script("""
                let menu = document.querySelector('[data-kt-element="theme-mode-menu"]');
                if (menu) {
                    let trigger = menu.parentElement.querySelector('[data-kt-menu-trigger]') || menu.previousElementSibling;
                    if (trigger && window.KTMenu) {
                        let item = window.KTMenu.getInstance(trigger) || window.KTMenu.getInstance(menu);
                        if (item) item.show(trigger);
                        else { menu.classList.add('show'); menu.style.display = 'block'; }
                    } else {
                        menu.classList.add('show'); menu.style.display = 'block';
                    }
                }
            """)
            sleep(2)  # Delay agar menu pilihan terlihat dalam mode gelap
            take_screenshot_session(driver, f"{session_prefix}_Theme_Menu_Open_Dark")

            # Klik pilihan Light (kembali ke default)
            driver.execute_script("""
                let lightBtn = document.querySelector('[data-kt-element="mode"][data-kt-value="light"]');
                if(lightBtn) lightBtn.click();
                let menu = document.querySelector('[data-kt-element="theme-mode-menu"]');
                if (menu) { menu.classList.remove('show'); menu.style.display = 'none'; }
            """)
            sleep(2)  # Delay animasi pergantian tema terang
    except Exception as e:
        print(f"      -> (Toggle Theme notice: {str(e).splitlines()[0]})")

def click_smart_link(driver, text):
    print(f"      -> Mencari tombol pintar: '{text}' pada sub-event yang berisi usulan...")
    try:
        el = driver.execute_script("""
            let text = arguments[0];
            let cards = Array.from(document.querySelectorAll('.ec-card, .card'));
            let inotekCard = cards.find(c => c.innerText.includes('INOTEK 2025') && c.innerText.includes(text));
            if(inotekCard) {
                let a = inotekCard.querySelector('a.btn') || inotekCard.querySelector('a');
                if(a) return a;
            }

            let validCards = cards.filter(c => 
                !c.innerText.includes('/ 0 sudah dinilai') && 
                !c.innerText.includes('/ 0 dinilai') && 
                !c.innerText.includes(' 0 total usulan') &&
                c.innerText.includes(text)
            );
            
            if(validCards.length > 0) {
                let a = validCards[0].querySelector('a.btn') || validCards[0].querySelector('a');
                if(a) return a;
            }
            
            let allA = Array.from(document.querySelectorAll('a, button'));
            return allA.find(a => a.innerText.includes(text)) || null;
        """, text)
        if el:
            highlight_and_click(driver, el, duration=1.0)
            return True
    except Exception as e:
        print(f"      -> (Pemberitahuan click_smart_link: {str(e).splitlines()[0]})")
    
    try:
        el = driver.find_element(By.XPATH, f"//a[contains(., '{text}')] | //button[contains(., '{text}')]")
        highlight_and_click(driver, el, duration=1.0)
    except Exception as err:
        print(f"      -> (Gagal menemukan smart link '{text}': {err})")

def clear_input(driver, element):
    try:
        driver.execute_script("arguments[0].focus();", element)
        element.clear()
        element.send_keys(Keys.CONTROL, 'a', Keys.BACK_SPACE)
        driver.execute_script("""
            arguments[0].value = '';
            arguments[0].dispatchEvent(new Event('input', { bubbles: true }));
            arguments[0].dispatchEvent(new Event('change', { bubbles: true }));
        """, element)
        sleep(0.2)
    except Exception:
        try:
            driver.execute_script("arguments[0].value = ''; arguments[0].dispatchEvent(new Event('input', { bubbles: true }));", element)
        except Exception:
            pass

def test_data_tables(driver, table_name_context="Data", search_keyword=None):
    print(f"      -> Uji DataTables (Sorting, Search, Paging) pada: {table_name_context}...")
    try:
        default_seeder_keywords = {
            'Riwayat Usulan': 'Diskominfo',
            'Rekap Nilai': 'Diskominfo',
            'Penilaian Tahap 1': 'BPBD',
            'Penilaian Tahap 2': 'LaporKita',
            'Kelola Usulan': 'Inovasi',
            'Event': 'INOTEK',
            'Sub Event': 'INOTEK',
            'Bidang': 'Teknologi',
            'User': 'Admin',
            'Penilai': 'Fauzi',
            'Pengumuman': 'Jadwal',
            'Indikator Tahap 1': 'INOTEK',
            'Detail Inovasi': 'Orisinalitas',
            'Detail Keterangan': 'Inovasi',
            'Indikator Tahap 2': 'INOTEK',
            'Detail Indikator Tahap 2': 'Presentasi',
            'Riwayat Inovasi': 'Diskominfo',
        }
        keyword = search_keyword or default_seeder_keywords.get(table_name_context, 'a')

        # 1. Search
        search_inputs = driver.find_elements(By.CSS_SELECTOR, '.dt-search input, .dataTables_filter input')
        if search_inputs and search_inputs[0].is_displayed():
            highlight_element(driver, search_inputs[0], duration=0.8)
            clear_input(driver, search_inputs[0])
            search_inputs[0].send_keys(keyword)
            sleep(1)
            take_screenshot_session(driver, f"{table_name_context.replace(' ', '_')}_Search")
            clear_input(driver, search_inputs[0])
            sleep(1)

        # 2. Length Menu
        length_selects = driver.find_elements(By.CSS_SELECTOR, '.dt-length select, .dataTables_length select')
        if length_selects and length_selects[0].is_displayed():
            highlight_element(driver, length_selects[0], duration=0.8)
            length_selects[0].send_keys('25')
            sleep(1)
            length_selects[0].send_keys('10')
            sleep(1)

        # 3. Sorting (Click first sortable column header twice)
        headers = driver.find_elements(By.CSS_SELECTOR, 'table.dataTable thead th.dt-orderable-asc, table.dataTable thead th.sorting, table.dataTable thead th.dt-ordering-asc')
        if headers and headers[0].is_displayed():
            highlight_and_click(driver, headers[0], duration=0.8)
            sleep(1)
            take_screenshot_session(driver, f"{table_name_context.replace(' ', '_')}_Sort_1")
            highlight_and_click(driver, headers[0], duration=0.8)
            sleep(1)

        # 4. Pagination
        next_btns = driver.find_elements(By.CSS_SELECTOR, '.dt-paging .next, .paginate_button.next')
        if next_btns and next_btns[0].is_displayed():
            class_name = next_btns[0].get_attribute('class') or ''
            if 'disabled' not in class_name:
                highlight_and_click(driver, next_btns[0], duration=0.8)
                sleep(1)
                prev_btns = driver.find_elements(By.CSS_SELECTOR, '.dt-paging .previous, .paginate_button.previous')
                if prev_btns and prev_btns[0].is_displayed():
                    highlight_and_click(driver, prev_btns[0], duration=0.8)
                    sleep(1)

        # 5. Column Visibility (ColVis)
        col_vis_btns = driver.find_elements(By.XPATH, "//button[contains(., 'Column visibility') or contains(@class, 'buttons-colvis')]")
        if col_vis_btns and col_vis_btns[0].is_displayed():
            highlight_and_click(driver, col_vis_btns[0], duration=0.8)
            sleep(1)
            
            col_vis_items = driver.find_elements(By.CSS_SELECTOR, '.dt-button-collection button.dt-button')
            if col_vis_items:
                highlight_and_click(driver, col_vis_items[0], duration=0.8)
                sleep(0.5)
                highlight_and_click(driver, col_vis_items[0], duration=0.8)
                sleep(0.5)
            
            driver.execute_script("""
                let bg = document.querySelector('.dt-button-background');
                if (bg) bg.click();
            """)
            driver.find_element(By.CSS_SELECTOR, 'body').send_keys(Keys.ESCAPE)
            sleep(0.5)
            driver.execute_script("""
                document.querySelectorAll('.dt-button-background, .dt-button-collection').forEach(el => el.remove());
            """)
            sleep(0.5)

        driver.execute_script("""
            document.querySelectorAll('.dt-button-background, .dt-button-collection').forEach(el => el.remove());
        """)
    except Exception as e:
        print(f"      -> (DataTables features not fully tested: {str(e).splitlines()[0]})")

def clear_input(driver, element):
    """Mengosongkan elemen input secara bersih."""
    try:
        if not element:
            return
        element.send_keys(Keys.CONTROL + "a")
        element.send_keys(Keys.BACKSPACE)
        element.clear()
    except Exception:
        pass

def isi_satu_input_per_substansi(driver, container_substansi, index_target_input, nilai_baru=None, batal=False):
    """
    Mengisi hanya 1 input per substansi (Mutual Exclusion per Indikator/Substansi).
    - Jika input baru diisi: input terisi sebelumnya dalam substansi ini otomatis dikosongkan.
    - Jika batal=True atau terjadi error: nilai input sebelumnya dikembalikan (Rollback).

    :param driver: WebDriver instance
    :param container_substansi: WebElement container per substansi (misal div.mb-4)
    :param index_target_input: Index input yang ingin diisi (0, 1, 2, dst.)
    :param nilai_baru: String/Int nilai baru yang akan diisi
    :param batal: Set True jika simulasi "tidak jadi / batal" (akan memicu Rollback)
    """
    inputs = container_substansi.find_elements(By.CSS_SELECTOR, "input.input-nilai-item, input[type='number']")
    if not inputs or index_target_input >= len(inputs):
        print(f"      -> (Index target input {index_target_input} tidak ditemukan dalam substansi)")
        return False

    target_input = inputs[index_target_input]

    # 1. SNAPSHOT / BACKUP: Catat input lama & nilainya sebelum diubah
    input_lama = None
    nilai_lama = ""

    for inp in inputs:
        val = inp.get_attribute('value') or ""
        if val != "" and val != "0":
            input_lama = inp
            nilai_lama = val
            break

    # 2. SKENARIO: BATAL / CANCEL (TIDAK JADI)
    if batal or nilai_baru is None:
        print("      -> ⚠️ Pengisian dibatalkan. Mengembalikan state awal...")
        clear_input(driver, target_input)
        
        if input_lama:
            highlight_element(driver, input_lama, duration=0.4)
            clear_input(driver, input_lama)
            input_lama.send_keys(str(nilai_lama))
        return False

    # 3. SKENARIO: JADI DIISI
    try:
        # Jika ada input lain yang terisi sebelumnya dan berbeda dari target, kosongkan!
        if input_lama and input_lama != target_input:
            print(f"      -> 🔄 Mengosongkan input sebelumnya (Nilai lama: {nilai_lama})...")
            highlight_element(driver, input_lama, duration=0.4)
            clear_input(driver, input_lama)

        # Highlight & Isikan nilai baru ke target input
        print(f"      -> ✍️ Mengisi nilai baru '{nilai_baru}' ke input target...")
        highlight_element(driver, target_input, duration=0.5)
        clear_input(driver, target_input)
        target_input.send_keys(str(nilai_baru))
        return True

    except Exception as err:
        # 4. ROLLBACK JIKA ERROR
        print(f"      -> ❌ Terjadi kesalahan ({err}), melakukan ROLLBACK ke nilai awal...")
        try:
            clear_input(driver, target_input)
            if input_lama:
                clear_input(driver, input_lama)
                input_lama.send_keys(str(nilai_lama))
        except Exception:
            pass
        raise err

