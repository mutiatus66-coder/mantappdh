import pytest
from selenium import webdriver
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.support.ui import WebDriverWait


@pytest.fixture(scope="function")
def driver():
    options = Options()
    options.add_argument("--start-maximized")
    options.add_argument("--disable-gpu")
    options.add_argument("--no-sandbox")
    options.add_argument("--disable-dev-shm-usage")
    options.add_experimental_option("excludeSwitches", ["enable-logging"])

    driver = webdriver.Chrome(options=options)
    driver.implicitly_wait(5)

    yield driver
    driver.quit()


@pytest.fixture(scope="function")
def wait(driver):
    return WebDriverWait(driver, 15)


@pytest.fixture(scope="function")
def base_url():
    return "http://localhost:8000"
