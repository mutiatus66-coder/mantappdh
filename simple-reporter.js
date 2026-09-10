class SimpleReporter {
  onTestEnd(test, result) {
    // Sembunyikan log untuk project "setup" agar lebih rapi
    if (test.parent && test.parent.project() && test.parent.project().name === 'setup') {
      return;
    }

    const icon = result.status === 'passed' ? '✅' : result.status === 'failed' ? '❌' : '⏭️';
    const duration = (result.duration / 1000).toFixed(1);
    
    // titlePath() mengembalikan array: ['', 'chromium', 'file.spec.js', 'Describe', 'Test Name']
    // Kita ambil index ke-3 dan seterusnya (menyembunyikan nama browser dan nama file)
    const titlePath = test.titlePath();
    const cleanTitle = titlePath.slice(3).join(' › ');
    
    console.log(`${icon} ${cleanTitle || test.title} (${duration}s)`);
  }
}

export default SimpleReporter;
